<?php

namespace App\Services\Exchange;

use App\Services\Exchange\Exceptions\ExchangeException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Binance adapter — spot on api.binance.com, USD-M futures on fapi.binance.com.
 *
 * Signing: HMAC-SHA256 over the exact query string that is transmitted, keyed by
 * the API secret, hex encoded. Auth travels in the X-MBX-APIKEY header.
 */
class BinanceService extends AbstractExchangeService
{
    private const SPOT = 'https://api.binance.com';
    private const FUTURES = 'https://fapi.binance.com';

    /**
     * Binance rejects a signed request whose timestamp drifts more than
     * recvWindow ms from its own clock.
     */
    private const RECV_WINDOW = 5000;

    /** Exchange filters are stable; a 10 minute cache avoids hammering them. */
    private const FILTER_TTL = 600;

    public function name(): string
    {
        return 'binance';
    }

    public function supportsFutures(): bool
    {
        return true;
    }

    /**
     * Host for a path. Spot and futures live on separate domains, so the base
     * is derived from the path rather than stored once.
     */
    private function baseUrl(string $path): string
    {
        return str_starts_with($path, '/fapi') ? self::FUTURES : self::SPOT;
    }

    /*
    |--------------------------------------------------------------------------
    | Auth + signing
    |--------------------------------------------------------------------------
    */

    protected function authPayload(string $method, string $path, array $params, string $body): array
    {
        $signed = $params;
        $signed['timestamp'] = $this->timestampMs();
        $signed['recvWindow'] = self::RECV_WINDOW;

        ksort($signed);

        // Binance verifies the signature by taking the query string it received,
        // stripping the signature parameter, and re-signing the remainder *in the
        // order it arrived*. Ordering is therefore part of the signature.
        //
        // So the string is built once, signed, and then transmitted verbatim
        // with the signature appended. Rebuilding it from the parameter array
        // would restore the caller's original key order and produce a valid-looking
        // request that fails with -1022 "Signature for this request is not valid".
        $query = http_build_query($signed, '', '&', PHP_QUERY_RFC3986);
        $signature = hash_hmac('sha256', $query, $this->apiSecret);

        return [
            'headers' => [
                'X-MBX-APIKEY' => $this->apiKey,
            ],
            // The signature rides as a query parameter on reads and writes alike,
            // so the complete signed exchange travels in the URL.
            'query' => $query . '&signature=' . $signature,
        ];
    }

    protected function extractError(array $decoded): array
    {
        return [
            isset($decoded['code']) ? (string) $decoded['code'] : null,
            $decoded['msg'] ?? null,
        ];
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
        $decoded = $this->signedRequest('GET', '/api/v3/account');

        $assets = [];

        foreach ($decoded['balances'] ?? [] as $row) {
            $free = (float) ($row['free'] ?? 0);
            $locked = (float) ($row['locked'] ?? 0);

            // Binance returns a zero row for every listed asset. Skipping them
            // keeps the wallet widget from rendering hundreds of empty lines.
            if ($free <= 0 && $locked <= 0) {
                continue;
            }

            $assets[] = [
                'asset' => (string) $row['asset'],
                'free' => $free,
                'locked' => $locked,
                'total' => $free + $locked,
                'usd_value' => 0.0,
            ];
        }

        return $this->valueAssets($assets, 'spot', $this->priceMap($assets, 'spot'));
    }

    private function fetchFuturesBalances(): array
    {
        $decoded = $this->signedRequest('GET', '/fapi/v2/balance');

        $assets = [];

        foreach ($decoded as $row) {
            if (!is_array($row) || !isset($row['asset'])) {
                continue;
            }

            $total = (float) ($row['balance'] ?? 0);

            if ($total <= 0) {
                continue;
            }

            // Futures wallets hold margin, not coins. The wallet balance is the
            // margin available; crossUnPnl is unrealised PnL, not spendable.
            $assets[] = [
                'asset' => (string) $row['asset'],
                'free' => (float) ($row['availableBalance'] ?? $row['balance'] ?? 0),
                'locked' => max(0.0, $total - (float) ($row['availableBalance'] ?? $total)),
                'total' => $total,
                'usd_value' => 0.0,
                'unrealised_pnl' => (float) ($row['crossUnPnl'] ?? 0),
            ];
        }

        return $this->valueAssets($assets, 'futures', $this->priceMap($assets, 'futures'));
    }

    /**
     * Bulk price lookup keyed by BASEQUOTE, matching the shared
     * valueAssets() contract.
     *
     * Spot batches through the `symbols` array parameter. Futures has no batch
     * equivalent — /fapi/v1/ticker/price accepts a single symbol or nothing at
     * all — so the full futures book is pulled once and cached briefly. Spot
     * and futures marks genuinely differ, so futures is not approximated with
     * the spot price.
     *
     * @param array<int, array> $assets Normalised asset rows.
     */
    private function priceMap(array $assets, string $marketType): array
    {
        $quote = $this->resolveQuoteAsset($assets);

        $symbols = [];

        foreach ($assets as $asset) {
            if ($asset['asset'] !== $quote) {
                $symbols[] = $this->normalizeSymbol($asset['asset'] . $quote);
            }
        }

        if ($symbols === []) {
            return [];
        }

        try {
            if ($marketType === 'futures') {
                return $this->futuresPriceMap($symbols);
            }

            $prices = [];

            foreach (array_chunk($symbols, 100) as $chunk) {
                // `symbols` is a JSON-encoded array, not a comma list; passing
                // "BTCUSDT,ETHUSDT" returns nothing and reads as an empty wallet.
                $decoded = $this->publicRequest('GET', '/api/v3/ticker/price', [
                    'symbols' => json_encode(array_values($chunk)),
                ]);

                foreach ((array) $decoded as $row) {
                    if (isset($row['symbol'], $row['price'])) {
                        $prices[$row['symbol']] = (float) $row['price'];
                    }
                }
            }

            return $prices;
        } catch (ExchangeException $e) {
            // A valuation failure must not lose the balance itself. The raw
            // amounts are still returned; only the USD figures read zero.
            Log::warning('binance.price_map_failed', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * @param array<int, string> $wanted
     * @return array<string, float>
     */
    private function futuresPriceMap(array $wanted): array
    {
        $book = Cache::remember('binance:futures:prices', 60, function () {
            $decoded = $this->publicRequest('GET', '/fapi/v1/ticker/price');

            $book = [];

            foreach ((array) $decoded as $row) {
                if (isset($row['symbol'], $row['price'])) {
                    $book[$row['symbol']] = (float) $row['price'];
                }
            }

            return $book;
        });

        $prices = [];

        foreach ($wanted as $symbol) {
            if (isset($book[$symbol])) {
                $prices[$symbol] = $book[$symbol];
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
        $symbol = $this->normalizeSymbol($symbol);
        $path = $marketType === 'futures' ? '/fapi/v1/ticker/24hr' : '/api/v3/ticker/24hr';

        $decoded = $this->publicRequest('GET', $path, ['symbol' => $symbol]);

        return [
            'last' => (float) ($decoded['lastPrice'] ?? 0),
            'bid' => isset($decoded['bidPrice']) ? (float) $decoded['bidPrice'] : null,
            'ask' => isset($decoded['askPrice']) ? (float) $decoded['askPrice'] : null,
            'change_percent' => isset($decoded['priceChangePercent'])
                ? (float) $decoded['priceChangePercent']
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
        $decoded = $this->signedRequest('POST', '/api/v3/order', $this->orderParams($params, false));

        return $this->normaliseOrder($decoded, $params);
    }

    public function placeFuturesOrder(array $params): array
    {
        $symbol = $this->normalizeSymbol($params['symbol']);
        $leverage = (int) ($params['leverage'] ?? 1);

        if ($leverage > 1) {
            // Setting leverage is idempotent but rate limited, so failures here
            // are logged rather than fatal — the order itself may still be fine
            // if the position already sits at the requested leverage.
            try {
                $this->setLeverage($symbol, $leverage, 'futures');
            } catch (ExchangeException $e) {
                Log::warning('binance.set_leverage_failed', [
                    'symbol' => $symbol,
                    'leverage' => $leverage,
                    'error' => $this->scrubSecretValues($e->getMessage()),
                ]);
            }
        }

        $decoded = $this->signedRequest('POST', '/fapi/v1/order', $this->orderParams($params, true));

        return $this->normaliseOrder($decoded, $params);
    }

    /**
     * Build the order payload shared by spot and futures, applying the symbol's
     * LOT_SIZE and PRICE_FILTER step sizes first.
     */
    private function orderParams(array $params, bool $futures): array
    {
        $symbol = $this->normalizeSymbol($params['symbol']);
        $type = strtolower($params['type'] ?? 'market');
        $quantity = $this->roundQuantity($symbol, (float) $params['quantity'], $futures ? 'futures' : 'spot');

        $payload = [
            'symbol' => $symbol,
            'side' => strtoupper($params['side']),
            'type' => strtoupper($type),
            'quantity' => (string) $quantity,
            'newClientOrderId' => $params['client_order_id'] ?? null,
        ];

        if ($type === 'limit') {
            $payload['price'] = (string) $this->roundPrice($symbol, (float) $params['price'], $futures ? 'futures' : 'spot');
            $payload['timeInForce'] = strtoupper($params['time_in_force'] ?? 'GTC');
        }

        if ($futures) {
            if (!empty($params['reduce_only'])) {
                $payload['reduceOnly'] = 'true';
            }

            if (!empty($params['close_position'])) {
                // Binance rejects closePosition alongside quantity.
                unset($payload['quantity']);
                $payload['closePosition'] = 'true';
            }
        }

        return array_filter($payload, fn ($value) => $value !== null);
    }

    private function normaliseOrder(array $decoded, array $params): array
    {
        $cumulative = (float) ($decoded['executedQty'] ?? 0);

        $averagePrice = 0.0;

        if ($cumulative > 0) {
            $quoteFilled = (float) ($decoded['cummulativeQuoteQty'] ?? 0);
            $averagePrice = $quoteFilled > 0 ? $quoteFilled / $cumulative : (float) ($decoded['price'] ?? 0);
        }

        return [
            'exchange_order_id' => isset($decoded['orderId']) ? (string) $decoded['orderId'] : null,
            'client_order_id' => (string) ($decoded['clientOrderId'] ?? ($params['client_order_id'] ?? '')),
            'status' => $this->mapStatus($decoded['status'] ?? ''),
            'filled_price' => $averagePrice > 0 ? $averagePrice : null,
            'filled_quantity' => $cumulative,
            'raw' => $this->redact($decoded),
        ];
    }

    private function mapStatus(string $binanceStatus): string
    {
        return match (strtoupper($binanceStatus)) {
            'NEW' => 'open',
            'PARTIALLY_FILLED' => 'open',
            'FILLED' => 'filled',
            'CANCELED', 'PENDING_CANCEL' => 'cancelled',
            'REJECTED', 'EXPIRED' => 'rejected',
            default => 'pending',
        };
    }

    public function setLeverage(string $symbol, int $leverage, string $marketType = 'futures'): array
    {
        if ($marketType !== 'futures') {
            throw new ExchangeException('Binance spot has no leverage', 400, 'unsupported_market');
        }

        $leverage = max(1, min(125, $leverage));

        return $this->signedRequest('POST', '/fapi/v1/leverage', [
            'symbol' => $this->normalizeSymbol($symbol),
            'leverage' => $leverage,
        ]);
    }

    public function cancelOrder(string $symbol, string $orderId, string $marketType = 'spot'): array
    {
        $path = $marketType === 'futures' ? '/fapi/v1/order' : '/api/v3/order';

        $decoded = $this->signedRequest('DELETE', $path, [
            'symbol' => $this->normalizeSymbol($symbol),
            'orderId' => $orderId,
        ]);

        return [
            'status' => $this->mapStatus($decoded['status'] ?? 'CANCELED'),
            'raw' => $this->redact($decoded),
        ];
    }

    public function closePosition(string $symbol, string $marketType = 'futures', ?float $quantity = null): array
    {
        if ($marketType !== 'futures') {
            throw new ExchangeException('Binance spot positions are closed by selling the balance', 400, 'unsupported_market');
        }

        $symbol = $this->normalizeSymbol($symbol);

        $payload = [
            'symbol' => $symbol,
            'side' => 'CLOSE_POSITION',
            'type' => 'MARKET',
            'newClientOrderId' => $this->clientOrderId('close'),
        ];

        if ($quantity !== null) {
            // Partial close uses reduceOnly instead of CLOSE_POSITION, because
            // Binance will not accept both.
            $payload = [
                'symbol' => $symbol,
                'side' => 'SELL',
                'type' => 'MARKET',
                'quantity' => (string) $this->roundQuantity($symbol, $quantity, 'futures'),
                'reduceOnly' => 'true',
                'newClientOrderId' => $this->clientOrderId('close'),
            ];
        }

        $decoded = $this->signedRequest('POST', '/fapi/v1/order', $payload);

        return $this->normaliseOrder($decoded, ['client_order_id' => $payload['newClientOrderId']]);
    }

    public function fetchOpenOrders(string $symbol, string $marketType = 'spot'): array
    {
        $path = $marketType === 'futures' ? '/fapi/v1/openOrders' : '/api/v3/openOrders';

        $decoded = $this->signedRequest('GET', $path, ['symbol' => $this->normalizeSymbol($symbol)]);

        $orders = [];

        foreach ((array) $decoded as $row) {
            if (!is_array($row)) {
                continue;
            }

            $orders[] = [
                'exchange_order_id' => (string) ($row['orderId'] ?? ''),
                'client_order_id' => $row['clientOrderId'] ?? null,
                'symbol' => $row['symbol'] ?? null,
                'side' => strtolower($row['side'] ?? ''),
                'type' => strtolower($row['type'] ?? ''),
                'price' => isset($row['price']) ? (float) $row['price'] : null,
                'quantity' => (float) ($row['origQty'] ?? 0),
                'filled_quantity' => (float) ($row['executedQty'] ?? 0),
                'status' => $this->mapStatus($row['status'] ?? ''),
            ];
        }

        return $orders;
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

        // Floor, never ceil: rounding up can exceed the available balance and
        // the exchange will reject the order.
        $steps = floor($quantity / $step);

        return round($steps * $step, 8);
    }

    public function roundPrice(string $symbol, float $price, string $marketType = 'spot'): float
    {
        $tick = $this->filterValue($symbol, $marketType, 'PRICE_FILTER', 'tickSize');

        if ($tick === null || $tick <= 0) {
            return round($price, 8);
        }

        return round(round($price / $tick) * $tick, 8);
    }

    /**
     * Read one exchange filter value, e.g. stepSize from LOT_SIZE.
     */
    private function filterValue(string $symbol, string $marketType, string $filterType, string $key): ?float
    {
        $symbol = $this->normalizeSymbol($symbol);
        $cacheKey = 'binance:filters:' . $marketType . ':' . $symbol;

        $filters = Cache::remember($cacheKey, self::FILTER_TTL, function () use ($symbol, $marketType) {
            $path = $marketType === 'futures' ? '/fapi/v1/exchangeInfo' : '/api/v3/exchangeInfo';

            try {
                $decoded = $this->publicRequest('GET', $path, ['symbol' => $symbol]);
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

    /*
    |--------------------------------------------------------------------------
    | Transport helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Signed request.
     *
     * The pre-built query string from authPayload() is sent exactly as signed.
     * Nothing downstream is allowed to re-encode it.
     */
    private function signedRequest(string $method, string $path, array $params = []): array
    {
        $auth = $this->authPayload($method, $path, $params, '');

        return $this->send(
            $method,
            $this->baseUrl($path) . $path,
            $auth['headers'] ?? [],
            $auth['query'],
            $path
        );
    }

    /**
     * Unauthenticated request, used for public market data and symbol filters.
     */
    private function publicRequest(string $method, string $path, array $params = []): array
    {
        // Unsigned, so parameter order carries no meaning here.
        return $this->send(
            $method,
            $this->baseUrl($path) . $path,
            [],
            http_build_query($params, '', '&', PHP_QUERY_RFC3986),
            $path
        );
    }

    /**
     * Single choke point for actually hitting the wire.
     *
     * Binance takes every parameter in the query string on reads and writes
     * alike, so there is exactly one encoding path and never a body. A JSON body
     * would be ignored by the exchange while detaching what was sent from what
     * was signed.
     *
     * @throws ExchangeException
     */
    private function send(string $method, string $url, array $headers, string $query, string $path): array
    {
        if ($query !== '') {
            $url .= (str_contains($url, '?') ? '&' : '?') . $query;
        }

        try {
            $request = $headers
                ? $this->pendingRequest()->withHeaders($headers)
                : $this->pendingRequest();

            $response = $request->send($method, $url);
        } catch (\Throwable $e) {
            throw $this->transportFailure($path, $e);
        }

        $decoded = $response->json();

        if ($response->successful()) {
            return is_array($decoded) ? $decoded : [];
        }

        $code = isset($decoded['code']) ? (string) $decoded['code'] : null;
        $message = $this->scrubOpaqueTokens($this->scrubSecretValues((string) ($decoded['msg'] ?? '')));

        Log::warning('binance.request_failed', [
            'path' => $path,
            'status' => $response->status(),
            'code' => $code,
            'message' => $message,
        ]);

        $context = ['path' => $path];

        // Binance tells us exactly how long to wait on a 429 via Retry-After.
        if ($retryAfter = $response->header('retry-after')) {
            $context['retry_after'] = (int) $retryAfter;
        }

        throw new ExchangeException(
            'Binance: ' . ($message ?: 'HTTP ' . $response->status()),
            $response->status(),
            $code,
            $context
        );
    }

    /**
     * Binance caps newClientOrderId at 36 characters.
     */
    private function clientOrderId(string $prefix): string
    {
        return substr($prefix . '_' . bin2hex(random_bytes(8)), 0, 36);
    }
}
