<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Exceptions\ExchangeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Bybit V5 adapter.
 *
 * Signing: HMAC-SHA256 over (timestamp + apiKey + recvWindow + payload), where
 * payload is the URL-encoded query for GET and the raw JSON body for POST.
 * Auth travels in the X-BAPI-* headers.
 *
 * Bybit quirk: API errors arrive as HTTP 200 with a non-zero retCode, so the
 * retCode check — not the status code — is what actually detects failure.
 */
class BybitService extends AbstractExchangeService
{
    private const BASE = 'https://api.bybit.com';

    private const RECV_WINDOW = 5000;

    private const FILTER_TTL = 600;

    public function name(): string
    {
        return 'bybit';
    }

    public function supportsFutures(): bool
    {
        return true;
    }

    protected function baseUrl(): string
    {
        return self::BASE;
    }

    /**
     * Bybit's product "category", not our market_type.
     */
    private function category(string $marketType): string
    {
        // This adapter targets USDT-margined linear perpetuals. Inverse
        // contracts would use "inverse" and settle in BTC.
        return $marketType === 'futures' ? 'linear' : 'spot';
    }

    /*
    |--------------------------------------------------------------------------
    | Auth + signing
    |--------------------------------------------------------------------------
    */

    protected function authPayload(string $method, string $path, array $params, string $body): array
    {
        $timestamp = (string) $this->timestampMs();
        $recvWindow = (string) self::RECV_WINDOW;

        $isWrite = !in_array(strtoupper($method), ['GET', 'HEAD'], true);

        // The exact string signed must be the exact string transmitted: the
        // query string for reads, the raw JSON body for writes.
        $payload = $isWrite
            ? $body
            : http_build_query($this->sorted($params), '', '&', PHP_QUERY_RFC3986);

        return [
            'headers' => [
                'X-BAPI-API-KEY' => $this->apiKey,
                'X-BAPI-TIMESTAMP' => $timestamp,
                'X-BAPI-RECEIVE-WINDOW' => $recvWindow,
                'X-BAPI-SIGN' => hash_hmac(
                    'sha256',
                    $timestamp . $this->apiKey . $recvWindow . $payload,
                    $this->apiSecret
                ),
            ],
        ];
    }

    protected function extractError(array $decoded): array
    {
        // retCode 0 means success. 10003 is "invalid api key", 10004 "invalid
        // sign", 10006 "invalid timestamp" — all credential or clock problems.
        $retCode = $decoded['retCode'] ?? null;

        if ($retCode === null || (int) $retCode === 0) {
            return [null, null];
        }

        return [
            (string) $retCode,
            $this->scrubOpaqueTokens($this->scrubSecretValues((string) ($decoded['retMsg'] ?? ''))),
        ];
    }

    private function sorted(array $params): array
    {
        ksort($params);

        return $params;
    }

    /*
    |--------------------------------------------------------------------------
    | Transport
    |--------------------------------------------------------------------------
    */

    /**
     * Issue a V5 request. Handles both HTTP-level and retCode-level failures.
     *
     * @throws ExchangeException
     */
    private function call(string $method, string $path, array $params = []): array
    {
        $isWrite = !in_array(strtoupper($method), ['GET', 'HEAD'], true);

        // Bybit requires alphabetically sorted parameters, on the wire and in
        // the signed string alike.
        $params = $this->sorted($params);

        $auth = $this->authPayload($method, $path, $params, $isWrite ? json_encode($params) : '');

        $url = self::BASE . $path
            . ($isWrite ? '' : '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));

        try {
            $response = $this->pendingRequest()
                ->withHeaders($auth['headers'])
                ->{$method}($url, $isWrite ? $params : []);
        } catch (\Throwable $e) {
            throw $this->transportFailure($path, $e);
        }

        $decoded = $response->json();

        if (!is_array($decoded)) {
            throw new ExchangeException(
                'Bybit returned a non-JSON response for ' . $method . ' ' . $path,
                $response->status(),
                'invalid_response'
            );
        }

        [$code, $message] = $this->extractError($decoded);

        if ($code !== null) {
            Log::warning('bybit.request_failed', [
                'path' => $path,
                'status' => $response->status(),
                'ret_code' => $code,
                'message' => $message,
            ]);

            throw new ExchangeException(
                'Bybit: ' . ($message ?: 'error ' . $code),
                $response->status(),
                $code,
                ['path' => $path]
            );
        }

        if (! $response->successful()) {
            throw new ExchangeException(
                'Bybit: HTTP ' . $response->status(),
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
        $decoded = $this->call('GET', '/v5/account/wallet-balance', [
            'accountType' => 'UNIFIED',
            'category' => $this->category($marketType),
        ]);

        $assets = [];

        foreach ($decoded['result']['list'] ?? [] as $row) {
            $coin = (string) ($row['coin'] ?? '');

            if ($coin === '') {
                continue;
            }

            // USDC on a linear account is a separate listing whose balance is
            // merged into the USDT wallet; skip it to avoid double counting.
            if (in_array($coin, ['USDC', 'BUSD'], true) && $marketType === 'futures') {
                continue;
            }

            $total = (float) ($row['walletBalance'] ?? 0);

            if ($total <= 0) {
                continue;
            }

            $free = (float) ($row['availableToTrade'] ?? $total);

            $assets[] = [
                'asset' => $coin,
                'free' => $free,
                'locked' => max(0.0, $total - $free),
                'total' => $total,
                'usd_value' => 0.0,
                'unrealised_pnl' => (float) ($row['unrealisedPnl'] ?? 0),
            ];
        }

        return $this->valueAssets($assets, $marketType, $this->priceMap($assets, $marketType));
    }

    /**
     * Bulk price lookup keyed by BASEQUOTE, matching the shared
     * valueAssets() contract.
     *
     * /v5/market/tickers takes a single `symbol` on linear and inverse, and
     * only spot accepts a comma list, so the loop below is the one shape that
     * behaves identically on all three categories. A wallet holds a handful of
     * assets, so the extra round trips are not worth a 500 KB full-book fetch.
     *
     * @param array<int, array> $assets Normalised asset rows.
     */
    private function priceMap(array $assets, string $marketType): array
    {
        $quote = $this->resolveQuoteAsset($assets);

        $prices = [];

        foreach ($assets as $asset) {
            if ($asset['asset'] === $quote) {
                continue;
            }

            $symbol = $this->normalizeSymbol($asset['asset'] . $quote);

            try {
                $decoded = $this->call('GET', '/v5/market/tickers', [
                    'category' => $this->category($marketType),
                    'symbol' => $symbol,
                ]);
            } catch (ExchangeException $e) {
                // Valuation is best effort; the balances themselves are still
                // valid and an unlisted pair simply contributes zero.
                continue;
            }

            $row = $decoded['result']['list'][0] ?? null;

            if (is_array($row) && isset($row['lastPrice'])) {
                $prices[$symbol] = (float) $row['lastPrice'];
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
        $decoded = $this->call('GET', '/v5/market/tickers', [
            'category' => $this->category($marketType),
            'symbol' => $this->normalizeSymbol($symbol),
        ]);

        $row = $decoded['result']['list'][0] ?? [];

        return [
            'last' => (float) ($row['lastPrice'] ?? 0),
            'bid' => isset($row['bid1Price']) ? (float) $row['bid1Price'] : null,
            'ask' => isset($row['ask1Price']) ? (float) $row['ask1Price'] : null,
            'change_percent' => isset($row['price24hPcnt'])
                ? round((float) $row['price24hPcnt'] * 100, 2)
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
        return $this->placeOrder($params, 'spot');
    }

    public function placeFuturesOrder(array $params): array
    {
        $symbol = $this->normalizeSymbol($params['symbol']);
        $leverage = (int) ($params['leverage'] ?? 1);

        if ($leverage > 1) {
            try {
                $this->setLeverage($symbol, $leverage, 'futures');
            } catch (ExchangeException $e) {
                Log::warning('bybit.set_leverage_failed', [
                    'symbol' => $symbol,
                    'leverage' => $leverage,
                    'error' => $this->scrubSecretValues($e->getMessage()),
                ]);
            }
        }

        return $this->placeOrder($params, 'futures');
    }

    private function placeOrder(array $params, string $marketType): array
    {
        $symbol = $this->normalizeSymbol($params['symbol']);
        $type = strtolower($params['type'] ?? 'market');

        $payload = [
            'category' => $this->category($marketType),
            'symbol' => $symbol,
            'side' => strtoupper($params['side']),
            'orderType' => $type === 'limit' ? 'Limit' : 'Market',
            'qty' => (string) $this->roundQuantity($symbol, (float) $params['quantity'], $marketType),
        ];

        if (!empty($params['client_order_id'])) {
            // Bybit caps orderLinkId at 36 characters.
            $payload['orderLinkId'] = substr((string) $params['client_order_id'], 0, 36);
        }

        if ($type === 'limit') {
            $payload['price'] = (string) $this->roundPrice($symbol, (float) $params['price'], $marketType);
            $payload['timeInForce'] = strtoupper($params['time_in_force'] ?? 'GTC');
        }

        if ($marketType === 'futures') {
            // 0 = one-way mode, which matches how this app tracks positions.
            $payload['positionIdx'] = 0;

            if (!empty($params['reduce_only'])) {
                $payload['reduceOnly'] = true;
            }

            // Bybit accepts take-profit and stop-loss inline with the order,
            // which is preferable to a separate trading-stop call.
            if (!empty($params['take_profit_price'])) {
                $payload['takeProfit'] = (string) $this->roundPrice($symbol, (float) $params['take_profit_price'], 'futures');
                $payload['tpTriggerBy'] = 'LastPrice';
            }

            if (!empty($params['stop_loss_price'])) {
                $payload['stopLoss'] = (string) $this->roundPrice($symbol, (float) $params['stop_loss_price'], 'futures');
                $payload['slTriggerBy'] = 'LastPrice';
            }
        }

        $decoded = $this->call('POST', '/v5/order/create', $payload);

        return $this->normaliseOrder($decoded, $params);
    }

    private function normaliseOrder(array $decoded, array $params): array
    {
        $result = $decoded['result'] ?? [];

        return [
            'exchange_order_id' => isset($result['orderId']) ? (string) $result['orderId'] : null,
            'client_order_id' => (string) ($result['orderLinkId'] ?? ($params['client_order_id'] ?? '')),
            // Bybit does not return a fill price on creation; the monitor
            // reconciles it from the execution feed.
            'status' => $this->mapStatus($result['orderStatus'] ?? ''),
            'filled_price' => null,
            'filled_quantity' => 0.0,
            'raw' => $this->redact($decoded),
        ];
    }

    private function mapStatus(string $bybitStatus): string
    {
        return match ($bybitStatus) {
            'New', 'PartiallyFilled', 'PendingNew' => 'open',
            'Filled' => 'filled',
            'Cancelled', 'PendingCancel' => 'cancelled',
            'Rejected', 'Expired' => 'rejected',
            default => 'pending',
        };
    }

    public function setLeverage(string $symbol, int $leverage, string $marketType = 'futures'): array
    {
        if ($marketType !== 'futures') {
            throw new ExchangeException('Bybit spot has no leverage', 400, 'unsupported_market');
        }

        $leverage = max(1, min(100, $leverage));

        return $this->call('POST', '/v5/position/set-leverage', [
            'category' => $this->category('futures'),
            'symbol' => $this->normalizeSymbol($symbol),
            'buyLeverage' => (string) $leverage,
            'sellLeverage' => (string) $leverage,
        ]);
    }

    public function cancelOrder(string $symbol, string $orderId, string $marketType = 'spot'): array
    {
        $decoded = $this->call('POST', '/v5/order/cancel', [
            'category' => $this->category($marketType),
            'symbol' => $this->normalizeSymbol($symbol),
            'orderId' => $orderId,
        ]);

        return [
            'status' => $this->mapStatus($decoded['result']['orderStatus'] ?? 'Cancelled'),
            'raw' => $this->redact($decoded),
        ];
    }

    public function closePosition(string $symbol, string $marketType = 'futures', ?float $quantity = null): array
    {
        if ($marketType !== 'futures') {
            throw new ExchangeException('Bybit spot balances are closed by selling them', 400, 'unsupported_market');
        }

        $symbol = $this->normalizeSymbol($symbol);

        // Bybit has no close-position endpoint; a reduceOnly market order on
        // the opposite side is the supported path. The side is derived because
        // the adapter is not told the position direction at this layer.
        $position = $this->fetchPosition($symbol);

        if ($position === null || $position['size'] == 0.0) {
            throw new ExchangeException('No open Bybit position for ' . $symbol, 404, 'no_position');
        }

        $side = $position['side'] === 'Buy' ? 'Sell' : 'Buy';

        $payload = [
            'category' => $this->category('futures'),
            'symbol' => $symbol,
            'side' => $side,
            'orderType' => 'Market',
            'qty' => (string) ($quantity !== null
                ? $this->roundQuantity($symbol, $quantity, 'futures')
                : $position['size']),
            'reduceOnly' => true,
            'positionIdx' => 0,
            'orderLinkId' => 'close_' . bin2hex(random_bytes(8)),
        ];

        $decoded = $this->call('POST', '/v5/order/create', $payload);

        return $this->normaliseOrder($decoded, ['client_order_id' => $payload['orderLinkId']]);
    }

    public function fetchOpenOrders(string $symbol, string $marketType = 'spot'): array
    {
        $decoded = $this->call('GET', '/v5/order/realtime', [
            'category' => $this->category($marketType),
            'symbol' => $this->normalizeSymbol($symbol),
            'openOnly' => 0,
        ]);

        $orders = [];

        foreach ($decoded['result']['list'] ?? [] as $row) {
            $orders[] = [
                'exchange_order_id' => (string) ($row['orderId'] ?? ''),
                'client_order_id' => $row['orderLinkId'] ?? null,
                'symbol' => $row['symbol'] ?? null,
                'side' => strtolower((string) ($row['side'] ?? '')),
                'type' => strtolower((string) ($row['orderType'] ?? '')),
                'price' => isset($row['price']) ? (float) $row['price'] : null,
                'quantity' => (float) ($row['qty'] ?? 0),
                'filled_quantity' => (float) ($row['cumExecQty'] ?? 0),
                'status' => $this->mapStatus((string) ($row['orderStatus'] ?? '')),
            ];
        }

        return $orders;
    }

    /**
     * Current linear position for a symbol, or null when flat.
     */
    private function fetchPosition(string $symbol): ?array
    {
        $decoded = $this->call('GET', '/v5/position/list', [
            'category' => $this->category('futures'),
            'symbol' => $this->normalizeSymbol($symbol),
        ]);

        $row = $decoded['result']['list'][0] ?? null;

        if (!is_array($row)) {
            return null;
        }

        return [
            'side' => (string) ($row['side'] ?? ''),
            'size' => (float) ($row['size'] ?? 0),
            'avg_price' => (float) ($row['avgPrice'] ?? 0),
            'unrealised_pnl' => (float) ($row['unrealisedPnl'] ?? 0),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Precision
    |--------------------------------------------------------------------------
    */

    public function roundQuantity(string $symbol, float $quantity, string $marketType = 'spot'): float
    {
        $step = $this->instrumentValue($symbol, $marketType, 'lotSizeFilter', 'qtyStep');

        if ($step === null || $step <= 0) {
            return round($quantity, 8);
        }

        return round(floor($quantity / $step) * $step, 8);
    }

    public function roundPrice(string $symbol, float $price, string $marketType = 'spot'): float
    {
        $tick = $this->instrumentValue($symbol, $marketType, 'priceFilter', 'tickSize');

        if ($tick === null || $tick <= 0) {
            return round($price, 8);
        }

        return round(round($price / $tick) * $tick, 8);
    }

    private function instrumentValue(string $symbol, string $marketType, string $filter, string $key): ?float
    {
        $symbol = $this->normalizeSymbol($symbol);
        $cacheKey = 'bybit:filters:' . $marketType . ':' . $symbol;

        $filters = Cache::remember($cacheKey, self::FILTER_TTL, function () use ($symbol, $marketType) {
            try {
                $decoded = $this->call('GET', '/v5/market/instruments-info', [
                    'category' => $this->category($marketType),
                    'symbol' => $symbol,
                ]);
            } catch (ExchangeException $e) {
                return null;
            }

            $row = $decoded['result']['list'][0] ?? null;

            return is_array($row) ? $row : null;
        });

        if (!is_array($filters) || !isset($filters[$filter][$key])) {
            return null;
        }

        return (float) $filters[$filter][$key];
    }
}
