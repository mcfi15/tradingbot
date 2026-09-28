<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Exceptions\ExchangeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * MEXC adapter — spot on api.mexc.com/api/v3, USD-M contracts on
 * api.mexc.com/api/v1/contract.
 *
 * Signing: MD5 of the alphabetically sorted parameter string with the secret
 * appended as a trailing "&secretKey=..." segment, upper-cased. Auth rides in
 * the request body/query as accessToken, timestamp and sign — MEXC does not use
 * auth headers.
 *
 * Two MEXC-specific hazards this adapter absorbs:
 *
 *  1. Contract symbols use an underscore (BTC_USDT) while spot symbols do not.
 *  2. Contract quantities are denominated in *contracts*, not base asset, so a
 *     naive pass-through of a BTC quantity would open a wildly oversized
 *     position. Every futures order is converted through contractSize.
 */
class MexcService extends AbstractExchangeService
{
    private const BASE = 'https://api.mexc.com';

    private const FILTER_TTL = 600;

    public function name(): string
    {
        return 'mexc';
    }

    public function supportsFutures(): bool
    {
        return true;
    }

    /**
     * MEXC contract symbols are BASE_QUOTE with an underscore, e.g. BTC_USDT.
     *
     * The base/quote split is resolved rather than guessed from a fixed width,
     * because quote assets differ in length (USDT, USDC) and base assets vary
     * from 2 to 5 characters.
     */
    private function contractSymbol(string $symbol): string
    {
        if (str_contains($symbol, '_')) {
            return strtoupper(trim($symbol));
        }

        [$base, $quote] = $this->splitSymbol($symbol);

        return $base . '_' . $quote;
    }

    /*
    |--------------------------------------------------------------------------
    | Auth + signing
    |--------------------------------------------------------------------------
    */

    protected function authPayload(string $method, string $path, array $params, string $body): array
    {
        $timestamp = (string) $this->timestampMs();

        $signed = array_merge($params, [
            'accessToken' => $this->apiKey,
            'timestamp' => $timestamp,
        ]);

        ksort($signed);

        // MEXC: MD5(sorted_params + "&secretKey=" + secret), upper-cased.
        $signature = strtoupper(
            md5(http_build_query($signed, '', '&', PHP_QUERY_RFC3986) . '&secretKey=' . $this->apiSecret)
        );

        return [
            'accessToken' => $this->apiKey,
            'timestamp' => $timestamp,
            'sign' => $signature,
        ];
    }

    protected function extractError(array $decoded): array
    {
        $code = $decoded['code'] ?? null;

        // Success is code 200 on the contract endpoints and the absence of an
        // error key on the v3 spot endpoints.
        if ($code === null || (int) $code === 200) {
            return [null, null];
        }

        return [
            (string) $code,
            $this->scrubOpaqueTokens($this->scrubSecretValues((string) ($decoded['msg'] ?? ''))),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Transport
    |--------------------------------------------------------------------------
    */

    private function call(string $method, string $path, array $params = [], bool $authenticated = true): array
    {
        if ($authenticated) {
            $params = array_merge($params, $this->authPayload($method, $path, $params, ''));
        }

        ksort($params);

        $url = self::BASE . $path;

        try {
            $response = $this->pendingRequest()->{$method}($url, $params);
        } catch (\Throwable $e) {
            throw $this->transportFailure($path, $e);
        }

        $decoded = $response->json();

        if (!is_array($decoded)) {
            throw new ExchangeException(
                'MEXC returned a non-JSON response for ' . $method . ' ' . $path,
                $response->status(),
                'invalid_response'
            );
        }

        [$code, $message] = $this->extractError($decoded);

        if ($code !== null) {
            Log::warning('mexc.request_failed', [
                'path' => $path,
                'status' => $response->status(),
                'code' => $code,
                'message' => $message,
            ]);

            throw new ExchangeException(
                'MEXC: ' . ($message ?: 'error ' . $code),
                $response->status(),
                $code,
                ['path' => $path]
            );
        }

        if (! $response->successful()) {
            throw new ExchangeException(
                'MEXC: HTTP ' . $response->status(),
                $response->status(),
                null,
                ['path' => $path]
            );
        }

        return $decoded;
    }

    /*
    |--------------------------------------------------------------------------
    | Balances
    |--------------------------------------------------------------------------
    */

    public function fetchBalances(string $marketType): array
    {
        if ($marketType === 'futures') {
            return $this->fetchFuturesBalances();
        }

        return $this->fetchSpotBalances();
    }

    private function fetchSpotBalances(): array
    {
        $decoded = $this->call('GET', '/api/v3/account');

        $assets = [];

        foreach ($decoded['balances'] ?? [] as $row) {
            $available = (float) ($row['available'] ?? 0);
            $locked = (float) ($row['locked'] ?? 0);

            if ($available <= 0 && $locked <= 0) {
                continue;
            }

            $assets[] = [
                'asset' => (string) $row['asset'],
                'free' => $available,
                'locked' => $locked,
                'total' => $available + $locked,
                'usd_value' => 0.0,
            ];
        }

        return $this->valueAssets($assets, 'spot', $this->priceMap($assets, 'spot'));
    }

    private function fetchFuturesBalances(): array
    {
        $decoded = $this->call('GET', '/api/v1/contract/account');

        $assets = [];

        foreach ($decoded['data']['assets'] ?? [] as $row) {
            $currency = (string) ($row['currency'] ?? '');

            if ($currency === '' || $currency === 'USDT') {
                // The USDT margin balance is added to the total below rather
                // than listed as a holding, so it is not double counted.
                continue;
            }

            $total = (float) ($row['walletBalance'] ?? 0);

            if ($total <= 0) {
                continue;
            }

            $assets[] = [
                'asset' => $currency,
                'free' => (float) ($row['available'] ?? $total),
                'locked' => max(0.0, $total - (float) ($row['available'] ?? $total)),
                'total' => $total,
                'usd_value' => 0.0,
                'unrealised_pnl' => (float) ($row['unrealisedPnl'] ?? 0),
            ];
        }

        $assets[] = [
            'asset' => 'USDT',
            'free' => (float) ($decoded['data']['available'] ?? 0),
            'locked' => 0.0,
            'total' => (float) ($decoded['data']['balance'] ?? 0),
            'usd_value' => (float) ($decoded['data']['balance'] ?? 0),
        ];

        return $this->valueAssets($assets, 'futures', $this->priceMap($assets, 'futures'));
    }

    /**
     * Bulk price lookup, keyed by BASEQUOTE to match the shared
     * valueAssets() contract.
     *
     * Spot tickers are used for both market types on purpose. A futures wallet
     * holds coin balances, not contracts, and not every coin MEXC lists as
     * margin also has a live perp — pricing off /api/v1/contract/ticker would
     * silently report a real holding as zero. The spot mark is a close enough
     * proxy for valuing a balance.
     *
     * @param array<int, array> $assets Normalised asset rows.
     */
    private function priceMap(array $assets, string $marketType): array
    {
        $quote = $this->resolveQuoteAsset($assets);

        $prices = [];

        foreach ($assets as $asset) {
            if ($asset['asset'] === $quote) {
                // The quote asset is its own price; valueAssets() handles that.
                continue;
            }

            $symbol = $this->normalizeSymbol($asset['asset'] . $quote);

            try {
                $decoded = $this->call('GET', '/api/v3/ticker/price', ['symbol' => $symbol], false);
            } catch (ExchangeException $e) {
                // Best effort: an unlisted pair contributes zero rather than
                // failing the whole balance read.
                continue;
            }

            if (isset($decoded['price'])) {
                $prices[$symbol] = (float) $decoded['price'];
            }
        }

        return $prices;
    }

    /*
    |--------------------------------------------------------------------------
    | Ticker
    |--------------------------------------------------------------------------
    */

    public function fetchTicker(string $symbol, string $marketType = 'spot'): array
    {
        $tickerSymbol = $marketType === 'futures'
            ? $this->contractSymbol($symbol)
            : $this->normalizeSymbol($symbol);

        $path = $marketType === 'futures'
            ? '/api/v1/contract/ticker'
            : '/api/v3/ticker/24hr';

        $decoded = $this->call('GET', $path, ['symbol' => $tickerSymbol], false);

        $row = $marketType === 'futures'
            ? ($decoded['data'] ?? [])
            : $decoded;

        return [
            'last' => (float) ($row['lastPrice'] ?? 0),
            'bid' => isset($row['bidPrice']) ? (float) $row['bidPrice'] : null,
            'ask' => isset($row['askPrice']) ? (float) $row['askPrice'] : null,
            'change_percent' => isset($row['priceChangePercent'])
                ? (float) $row['priceChangePercent']
                : null,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Orders
    |--------------------------------------------------------------------------
    */

    public function placeSpotOrder(array $params): array
    {
        $symbol = $this->normalizeSymbol($params['symbol']);
        $type = strtolower($params['type'] ?? 'market');

        $payload = [
            'symbol' => $symbol,
            'side' => strtoupper($params['side']),
            'type' => strtoupper($type),
            'quantity' => (string) $this->roundQuantity($symbol, (float) $params['quantity'], 'spot'),
        ];

        if (!empty($params['client_order_id'])) {
            $payload['newClientOrderId'] = substr((string) $params['client_order_id'], 0, 40);
        }

        if ($type === 'limit') {
            $payload['price'] = (string) $this->roundPrice($symbol, (float) $params['price'], 'spot');
        }

        $decoded = $this->call('POST', '/api/v3/order', $payload);

        return $this->normaliseSpotOrder($decoded, $params);
    }

    public function placeFuturesOrder(array $params): array
    {
        $symbol = $this->normalizeSymbol($params['symbol']);
        $type = strtolower($params['type'] ?? 'market');

        $leverage = max(1, min(100, (int) ($params['leverage'] ?? 1)));

        // Convert a base-asset quantity into contracts. Skipping this would
        // mean asking for 0.01 contracts of BTC, i.e. almost nothing, or
        // interpreting 0.01 BTC as 0.01 contracts = 1 whole BTC.
        $contracts = $this->toContracts(
            $symbol,
            $this->roundQuantity($symbol, (float) $params['quantity'], 'futures')
        );

        if ($contracts <= 0) {
            throw new ExchangeException(
                'MEXC contract size rounds ' . $params['quantity'] . ' ' . $symbol . ' down to zero contracts',
                400,
                'quantity_too_small'
            );
        }

        $payload = [
            'symbol' => $this->contractSymbol($symbol),
            'side' => strtoupper($params['side']) === 'BUY' ? 1 : 2,
            'type' => $type === 'limit' ? 2 : 1,
            'quantity' => $contracts,
            'openType' => !empty($params['reduce_only']) ? 2 : 0,
            'positionType' => 1,
            'leverage' => $leverage,
        ];

        if (!empty($params['client_order_id'])) {
            $payload['newClientOrderId'] = substr((string) $params['client_order_id'], 0, 40);
        }

        if ($type === 'limit') {
            $payload['price'] = $this->roundPrice($symbol, (float) $params['price'], 'futures');
        }

        $decoded = $this->call('POST', '/api/v1/contract/order', $payload);

        return $this->normaliseContractOrder($decoded, $params);
    }

    private function normaliseSpotOrder(array $decoded, array $params): array
    {
        return [
            'exchange_order_id' => isset($decoded['orderId']) ? (string) $decoded['orderId'] : null,
            'client_order_id' => (string) ($decoded['clientOrderId'] ?? ($params['client_order_id'] ?? '')),
            'status' => $this->mapStatus($decoded['status'] ?? ''),
            'filled_price' => isset($decoded['price']) && (float) $decoded['price'] > 0
                ? (float) $decoded['price']
                : null,
            'filled_quantity' => (float) ($decoded['executedQty'] ?? 0),
            'raw' => $this->redact($decoded),
        ];
    }

    private function normaliseContractOrder(array $decoded, array $params): array
    {
        $data = $decoded['data'] ?? [];

        // Contract quantities come back in contracts; the rest of the system
        // speaks in base asset, so convert on the way out.
        $filledBase = 0.0;

        if (isset($params['symbol']) && isset($data['dealSize'])) {
            try {
                $filledBase = $this->toBaseQuantity($params['symbol'], (float) $data['dealSize']);
            } catch (ExchangeException $e) {
                $filledBase = 0.0;
            }
        }

        return [
            'exchange_order_id' => isset($data['orderId']) ? (string) $data['orderId'] : null,
            'client_order_id' => (string) ($data['clientOid'] ?? ($params['client_order_id'] ?? '')),
            // The contract order endpoint returns no status; a non-null orderId
            // means accepted.
            'status' => isset($data['orderId']) ? 'open' : 'pending',
            'filled_price' => isset($data['price']) && (float) $data['price'] > 0
                ? (float) $data['price']
                : null,
            'filled_quantity' => $filledBase,
            'raw' => $this->redact($decoded),
        ];
    }

    private function mapStatus(string $mexcStatus): string
    {
        return match (strtoupper($mexcStatus)) {
            'NEW' => 'open',
            'PARTIALLY_FILLED' => 'open',
            'FILLED' => 'filled',
            'CANCELED', 'CANCELLED' => 'cancelled',
            'REJECTED', 'EXPIRED' => 'rejected',
            default => 'pending',
        };
    }

    public function setLeverage(string $symbol, int $leverage, string $marketType = 'futures'): array
    {
        if ($marketType !== 'futures') {
            throw new ExchangeException('MEXC spot has no leverage', 400, 'unsupported_market');
        }

        $leverage = max(1, min(100, $leverage));

        // MEXC has no dedicated leverage endpoint; leverage is set per order or
        // via the risk-limit adjustment. Applying it on the next order is the
        // supported mechanism, so this is a documented no-op.
        return [
            'symbol' => $this->contractSymbol($symbol),
            'leverage' => $leverage,
            'applied' => 'on_next_order',
        ];
    }

    public function cancelOrder(string $symbol, string $orderId, string $marketType = 'spot'): array
    {
        $path = $marketType === 'futures' ? '/api/v1/contract/order' : '/api/v3/order';

        $decoded = $this->call('DELETE', $path, [
            'symbol' => $marketType === 'futures'
                ? $this->contractSymbol($symbol)
                : $this->normalizeSymbol($symbol),
            'orderId' => $orderId,
        ]);

        return [
            'status' => 'cancelled',
            'raw' => $this->redact($decoded),
        ];
    }

    public function closePosition(string $symbol, string $marketType = 'futures', ?float $quantity = null): array
    {
        if ($marketType !== 'futures') {
            throw new ExchangeException('MEXC spot balances are closed by selling them', 400, 'unsupported_market');
        }

        $contracts = $quantity !== null
            ? $this->toContracts($symbol, $quantity)
            : $this->openContractQuantity($symbol);

        if ($contracts <= 0) {
            throw new ExchangeException('No open MEXC position for ' . $symbol, 404, 'no_position');
        }

        $position = $this->fetchContractPosition($symbol);

        $payload = [
            'symbol' => $this->contractSymbol($symbol),
            // Close must trade against the open side.
            'side' => (($position['holdSide'] ?? 1) == 1) ? 2 : 1,
            'type' => 1,
            'quantity' => $contracts,
            'openType' => 2,
            'positionType' => 1,
        ];

        $decoded = $this->call('POST', '/api/v1/contract/order', $payload);

        return $this->normaliseContractOrder($decoded, [
            'symbol' => $symbol,
            'client_order_id' => null,
        ]);
    }

    public function fetchOpenOrders(string $symbol, string $marketType = 'spot'): array
    {
        $path = $marketType === 'futures' ? '/api/v1/contract/open_orders' : '/api/v3/openOrders';

        $decoded = $this->call('GET', $path, [
            'symbol' => $marketType === 'futures'
                ? $this->contractSymbol($symbol)
                : $this->normalizeSymbol($symbol),
        ]);

        $rows = $marketType === 'futures' ? ($decoded['data'] ?? []) : $decoded;

        $orders = [];

        foreach ((array) $rows as $row) {
            $orders[] = [
                'exchange_order_id' => (string) ($row['orderId'] ?? ''),
                'client_order_id' => $row['clientOid'] ?? ($row['clientOrderId'] ?? null),
                'symbol' => $row['symbol'] ?? null,
                'side' => isset($row['side']) && is_numeric($row['side'])
                    ? ((int) $row['side'] === 1 ? 'buy' : 'sell')
                    : strtolower((string) ($row['side'] ?? '')),
                'type' => isset($row['orderType']) && is_numeric($row['orderType'])
                    ? ((int) $row['orderType'] === 2 ? 'limit' : 'market')
                    : strtolower((string) ($row['type'] ?? '')),
                'price' => isset($row['price']) ? (float) $row['price'] : null,
                'quantity' => (float) ($row['quantity'] ?? 0),
                'filled_quantity' => (float) ($row['filledQty'] ?? ($row['executedQty'] ?? 0)),
                'status' => $this->mapStatus((string) ($row['state'] ?? ($row['status'] ?? ''))),
            ];
        }

        return $orders;
    }

    /*
    |--------------------------------------------------------------------------
    | Contract helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Contract size (base asset per contract) for a symbol.
     */
    private function contractSize(string $symbol): ?float
    {
        $contractSymbol = $this->contractSymbol($symbol);
        $cacheKey = 'mexc:contract:' . $contractSymbol;

        $size = Cache::remember($cacheKey, self::FILTER_TTL, function () use ($contractSymbol) {
            try {
                $decoded = $this->call('GET', '/api/v1/contract/detail', [
                    'symbol' => $contractSymbol,
                ], false);
            } catch (ExchangeException $e) {
                return null;
            }

            $row = $decoded['data'][0] ?? null;

            return isset($row['contractSize']) ? (float) $row['contractSize'] : null;
        });

        return $size !== null ? (float) $size : null;
    }

    private function toContracts(string $symbol, float $baseQuantity): float
    {
        $size = $this->contractSize($symbol);

        if ($size === null || $size <= 0) {
            // Refusing is safer than guessing. Guessing low silently trades
            // ~nothing; guessing high is catastrophic.
            throw new ExchangeException(
                'Unknown MEXC contract size for ' . $symbol . '; refusing to guess position size',
                400,
                'contract_size_unknown'
            );
        }

        return floor($baseQuantity / $size);
    }

    private function toBaseQuantity(string $symbol, float $contracts): float
    {
        $size = $this->contractSize($symbol);

        if ($size === null || $size <= 0) {
            return 0.0;
        }

        return round($contracts * $size, 8);
    }

    private function openContractQuantity(string $symbol): float
    {
        $position = $this->fetchContractPosition($symbol);

        return (float) ($position['position'] ?? 0);
    }

    /**
     * Current contract position, or an empty array when flat.
     */
    private function fetchContractPosition(string $symbol): array
    {
        try {
            $decoded = $this->call('GET', '/api/v1/contract/position', [
                'symbol' => $this->contractSymbol($symbol),
            ]);
        } catch (ExchangeException $e) {
            return [];
        }

        $row = $decoded['data'][0] ?? null;

        return is_array($row) ? $row : [];
    }

    /*
    |--------------------------------------------------------------------------
    | Precision
    |--------------------------------------------------------------------------
    */

    public function roundQuantity(string $symbol, float $quantity, string $marketType = 'spot'): float
    {
        $step = $this->filterValue($symbol, $marketType, 'LOT_SIZE', 'stepSize');

        if ($step === null || $step <= 0) {
            return round($quantity, 8);
        }

        return round(floor($quantity / $step) * $step, 8);
    }

    public function roundPrice(string $symbol, float $price, string $marketType = 'spot'): float
    {
        $tick = $this->filterValue($symbol, $marketType, 'PRICE_FILTER', 'tickSize');

        if ($tick === null || $tick <= 0) {
            return round($price, 8);
        }

        return round(round($price / $tick) * $tick, 8);
    }

    private function filterValue(string $symbol, string $marketType, string $filterType, string $key): ?float
    {
        $target = $marketType === 'futures'
            ? $this->contractSymbol($symbol)
            : $this->normalizeSymbol($symbol);

        $cacheKey = 'mexc:filters:' . $marketType . ':' . $target;

        $filters = Cache::remember($cacheKey, self::FILTER_TTL, function () use ($target) {
            try {
                $decoded = $this->call('GET', '/api/v3/exchangeInfo', ['symbol' => $target], false);
            } catch (ExchangeException $e) {
                return null;
            }

            $entry = $decoded['symbols'][0] ?? null;

            if (!is_array($entry)) {
                return null;
            }

            $map = [];

            foreach ($entry['filters'] ?? [] as $filter) {
                if (isset($filter['filterType'], $filter[$key])) {
                    $map[$filter['filterType']] = (float) $filter[$key];
                }
            }

            return $map;
        });

        return is_array($filters) && isset($filters[$filterType]) ? (float) $filters[$filterType] : null;
    }
}
