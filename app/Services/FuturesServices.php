<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FuturesServices
{
    /**
     * Provider endpoint configurations.
     */
    protected array $providers = [
        'binance_futures' => [
            'type' => 'binance_like',
            'tickers_url' => 'https://fapi.binance.com/fapi/v1/ticker/24hr',
            'ticker_url' => 'https://fapi.binance.com/fapi/v1/ticker/24hr',
            'trades_url' => 'https://fapi.binance.com/fapi/v1/trades',
            'depth_url' => 'https://fapi.binance.com/fapi/v1/depth',
        ],
        'binance_spot' => [
            'type' => 'binance_like',
            'tickers_url' => 'https://api.binance.com/api/v3/ticker/24hr',
            'ticker_url' => 'https://api.binance.com/api/v3/ticker/24hr',
            'trades_url' => 'https://api.binance.com/api/v3/trades',
            'depth_url' => 'https://api.binance.com/api/v3/depth',
        ],
        'binance_us' => [
            'type' => 'binance_like',
            'tickers_url' => 'https://api.binance.us/api/v3/ticker/24hr',
            'ticker_url' => 'https://api.binance.us/api/v3/ticker/24hr',
            'trades_url' => 'https://api.binance.us/api/v3/trades',
            'depth_url' => 'https://api.binance.us/api/v3/depth',
        ],
        'bybit_linear' => [
            'type' => 'bybit',
            'tickers_url' => 'https://api.bybit.com/v5/market/tickers?category=linear',
            'ticker_url' => 'https://api.bybit.com/v5/market/tickers?category=linear',
            'trades_url' => 'https://api.bybit.com/v5/market/recent-trade?category=linear',
            'depth_url' => 'https://api.bybit.com/v5/market/orderbook?category=linear',
        ],
    ];

    /**
     * Default popular crypto pairs.
     */
    protected array $defaultPairs = [
        'BTCUSDT',
        'ETHUSDT',
        'SOLUSDT',
        'BNBUSDT',
        'XRPUSDT',
        'DOGEUSDT',
        'ADAUSDT',
        'AVAXUSDT',
        'LINKUSDT',
        'DOTUSDT',
        'NEARUSDT',
        'MATICUSDT',
        'LTCUSDT',
        'SHIBUSDT',
        'PEPEUSDT',
        'UNIUSDT',
        'SUIUSDT',
        'APTUSDT',
        'ARBUSDT',
        'OPUSDT'
    ];

    /**
     * Fetch all crypto tickers (USDT pairs) with 24h market stats.
     * Automatically fails over across Binance Global, Binance US, and Bybit.
     *
     * @return array
     */
    public function futureTickers(): array
    {
        $cacheKey = 'futures_market_tickers_unified';
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $activeProvider = Cache::get('active_market_provider');
        $providerOrder = array_keys($this->providers);

        if ($activeProvider && in_array($activeProvider, $providerOrder)) {
            // Place known healthy active provider first
            $providerOrder = array_diff($providerOrder, [$activeProvider]);
            array_unshift($providerOrder, $activeProvider);
        }

        foreach ($providerOrder as $providerKey) {
            $provider = $this->providers[$providerKey];

            try {
                $response = Http::withoutVerifying()
                    ->timeout(6)
                    ->get($provider['tickers_url']);

                // If geo-blocked (451 / 403) or failed, continue to next provider
                if ($response->status() === 451 || $response->status() === 403 || !$response->successful()) {
                    continue;
                }

                $formatted = [];
                if ($provider['type'] === 'binance_like') {
                    $raw = $response->json();
                    if (is_array($raw)) {
                        foreach ($raw as $item) {
                            $symbol = $item['symbol'] ?? '';
                            if ($this->isValidUsdtPair($symbol)) {
                                $lastPrice = (float)($item['lastPrice'] ?? 0);
                                if ($lastPrice > 0) {
                                    $formatted[] = [
                                        'ticker' => $symbol,
                                        'name' => str_replace('USDT', ' / USDT', $symbol),
                                        'current_price' => $lastPrice,
                                        'change_1d_percentage' => round((float)($item['priceChangePercent'] ?? 0), 2),
                                        'high_24h' => (float)($item['highPrice'] ?? 0),
                                        'low_24h' => (float)($item['lowPrice'] ?? 0),
                                        'volume_24h' => (float)($item['volume'] ?? ($item['quoteVolume'] ?? 0)),
                                    ];
                                }
                            }
                        }
                    }
                } elseif ($provider['type'] === 'bybit') {
                    $raw = $response->json()['result']['list'] ?? [];
                    if (is_array($raw)) {
                        foreach ($raw as $item) {
                            $symbol = $item['symbol'] ?? '';
                            if ($this->isValidUsdtPair($symbol)) {
                                $lastPrice = (float)($item['lastPrice'] ?? 0);
                                if ($lastPrice > 0) {
                                    $changePercent = (float)($item['price24hPcnt'] ?? 0) * 100;
                                    $formatted[] = [
                                        'ticker' => $symbol,
                                        'name' => str_replace('USDT', ' / USDT', $symbol),
                                        'current_price' => $lastPrice,
                                        'change_1d_percentage' => round($changePercent, 2),
                                        'high_24h' => (float)($item['highPrice24h'] ?? 0),
                                        'low_24h' => (float)($item['lowPrice24h'] ?? 0),
                                        'volume_24h' => (float)($item['volume24h'] ?? ($item['turnover24h'] ?? 0)),
                                    ];
                                }
                            }
                        }
                    }
                }

                if (!empty($formatted)) {
                    $this->sortTickers($formatted);
                    $data = [
                        'status' => 'success',
                        'data' => array_values($formatted),
                        'provider' => $providerKey,
                        'code' => 200,
                    ];

                    Cache::put('active_market_provider', $providerKey, now()->addHours(1));
                    Cache::put($cacheKey, $data, now()->addSeconds(10));
                    return $data;
                }
            } catch (\Exception $e) {
                Log::debug("FuturesServices provider {$providerKey} ticker fetch failed: " . $e->getMessage());
            }
        }

        // Return fallback data if all networks fail
        return $this->getFallbackTickers();
    }

    /**
     * Get single ticker data.
     *
     * @param string $ticker
     * @return array
     */
    public function futureTicker(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));
        $cacheKey = 'futures_market_ticker_' . $ticker;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // Check if cached in full list
        $all = $this->futureTickers();
        if ($all['status'] === 'success') {
            foreach ($all['data'] as $item) {
                if ($item['ticker'] === $ticker) {
                    $result = ['status' => 'success', 'data' => $item, 'code' => 200];
                    Cache::put($cacheKey, $result, now()->addSeconds(10));
                    return $result;
                }
            }
        }

        return [
            'status' => 'error',
            'message' => __('Market data currently unavailable for :ticker', ['ticker' => $ticker]),
            'code' => 404,
        ];
    }

    /**
     * Get recent trades for ticker.
     *
     * @param string $ticker
     * @return array
     */
    public function futuresRecentTrades(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));
        $cacheKey = 'futures_market_trades_' . $ticker;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $activeProvider = Cache::get('active_market_provider', 'binance_us');
        $providerOrder = array_keys($this->providers);

        if (in_array($activeProvider, $providerOrder)) {
            $providerOrder = array_diff($providerOrder, [$activeProvider]);
            array_unshift($providerOrder, $activeProvider);
        }

        foreach ($providerOrder as $providerKey) {
            $provider = $this->providers[$providerKey];

            try {
                $url = $provider['trades_url'];
                $params = ['limit' => 20];

                if ($provider['type'] === 'binance_like') {
                    $params['symbol'] = $ticker;
                } elseif ($provider['type'] === 'bybit') {
                    $params['symbol'] = $ticker;
                }

                $response = Http::withoutVerifying()->timeout(5)->get($url, $params);
                if ($response->successful()) {
                    $raw = $response->json();
                    $trades = [];

                    if ($provider['type'] === 'binance_like' && is_array($raw)) {
                        foreach ($raw as $t) {
                            $trades[] = [
                                'price' => (float)($t['price'] ?? 0),
                                'qty' => (float)($t['qty'] ?? 0),
                                'time' => $t['time'] ?? (int)(microtime(true) * 1000),
                                'is_buyer_maker' => $t['isBuyerMaker'] ?? false,
                            ];
                        }
                    } elseif ($provider['type'] === 'bybit') {
                        $list = $raw['result']['list'] ?? [];
                        foreach ($list as $t) {
                            $trades[] = [
                                'price' => (float)($t['price'] ?? 0),
                                'qty' => (float)($t['size'] ?? 0),
                                'time' => (int)($t['time'] ?? (microtime(true) * 1000)),
                                'is_buyer_maker' => ($t['side'] ?? '') === 'Sell',
                            ];
                        }
                    }

                    if (!empty($trades)) {
                        $data = [
                            'status' => 'success',
                            'data' => $trades,
                            'code' => 200,
                        ];

                        Cache::put($cacheKey, $data, now()->addSeconds(6));
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                // Continue to next provider
            }
        }

        return ['status' => 'success', 'data' => $this->getFallbackTrades(), 'code' => 200];
    }

    /**
     * Get order book depth for ticker.
     *
     * @param string $ticker
     * @return array
     */
    public function futuresOrderBook(string $ticker): array
    {
        $ticker = strtoupper(trim($ticker));
        $cacheKey = 'futures_market_depth_' . $ticker;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $activeProvider = Cache::get('active_market_provider', 'binance_us');
        $providerOrder = array_keys($this->providers);

        if (in_array($activeProvider, $providerOrder)) {
            $providerOrder = array_diff($providerOrder, [$activeProvider]);
            array_unshift($providerOrder, $activeProvider);
        }

        foreach ($providerOrder as $providerKey) {
            $provider = $this->providers[$providerKey];

            try {
                $url = $provider['depth_url'];
                $params = ['limit' => 15];

                if ($provider['type'] === 'binance_like') {
                    $params['symbol'] = $ticker;
                } elseif ($provider['type'] === 'bybit') {
                    $params['symbol'] = $ticker;
                }

                $response = Http::withoutVerifying()->timeout(5)->get($url, $params);
                if ($response->successful()) {
                    $raw = $response->json();
                    $bids = [];
                    $asks = [];

                    if ($provider['type'] === 'binance_like') {
                        $bids = $raw['bids'] ?? [];
                        $asks = $raw['asks'] ?? [];
                    } elseif ($provider['type'] === 'bybit') {
                        $bids = $raw['result']['b'] ?? [];
                        $asks = $raw['result']['a'] ?? [];
                    }

                    $data = [
                        'status' => 'success',
                        'data' => [
                            'bids' => $bids,
                            'asks' => $asks,
                        ],
                        'code' => 200,
                    ];

                    Cache::put($cacheKey, $data, now()->addSeconds(6));
                    return $data;
                }
            } catch (\Exception $e) {
                // Continue to next provider
            }
        }

        return ['status' => 'success', 'data' => ['bids' => [], 'asks' => []], 'code' => 200];
    }

    /**
     * Check if a symbol is a valid USDT trading pair.
     */
    protected function isValidUsdtPair(string $symbol): bool
    {
        return str_ends_with($symbol, 'USDT')
            && !str_contains($symbol, '_')
            && !in_array($symbol, ['USDCUSDT', 'FDUSDUSDT', 'TUSDUSDT', 'EURUSDT', 'BUSDUSDT']);
    }

    /**
     * Sort tickers prioritizing default watchlist pairs and 24h volume.
     */
    protected function sortTickers(array &$tickers): void
    {
        usort($tickers, function ($a, $b) {
            $aIsDefault = in_array($a['ticker'], $this->defaultPairs);
            $bIsDefault = in_array($b['ticker'], $this->defaultPairs);
            if ($aIsDefault && !$bIsDefault) return -1;
            if (!$aIsDefault && $bIsDefault) return 1;
            return $b['volume_24h'] <=> $a['volume_24h'];
        });
    }

    /**
     * Fallback tickers in case of network or rate limit issues.
     */
    protected function getFallbackTickers(): array
    {
        $fallbacks = [
            ['ticker' => 'BTCUSDT', 'name' => 'BTC / USDT', 'current_price' => 96500.00, 'change_1d_percentage' => 2.45, 'high_24h' => 97800.00, 'low_24h' => 94200.00, 'volume_24h' => 45000.5],
            ['ticker' => 'ETHUSDT', 'name' => 'ETH / USDT', 'current_price' => 2850.00, 'change_1d_percentage' => 1.80, 'high_24h' => 2920.00, 'low_24h' => 2780.00, 'volume_24h' => 125000.0],
            ['ticker' => 'SOLUSDT', 'name' => 'SOL / USDT', 'current_price' => 195.50, 'change_1d_percentage' => 4.20, 'high_24h' => 202.00, 'low_24h' => 188.00, 'volume_24h' => 380000.0],
            ['ticker' => 'BNBUSDT', 'name' => 'BNB / USDT', 'current_price' => 645.00, 'change_1d_percentage' => 0.95, 'high_24h' => 655.00, 'low_24h' => 638.00, 'volume_24h' => 65000.0],
            ['ticker' => 'XRPUSDT', 'name' => 'XRP / USDT', 'current_price' => 2.35, 'change_1d_percentage' => 3.10, 'high_24h' => 2.48, 'low_24h' => 2.22, 'volume_24h' => 950000.0],
            ['ticker' => 'DOGEUSDT', 'name' => 'DOGE / USDT', 'current_price' => 0.28, 'change_1d_percentage' => -1.25, 'high_24h' => 0.31, 'low_24h' => 0.27, 'volume_24h' => 1200000.0],
            ['ticker' => 'ADAUSDT', 'name' => 'ADA / USDT', 'current_price' => 0.85, 'change_1d_percentage' => 1.15, 'high_24h' => 0.89, 'low_24h' => 0.82, 'volume_24h' => 540000.0],
            ['ticker' => 'AVAXUSDT', 'name' => 'AVAX / USDT', 'current_price' => 34.20, 'change_1d_percentage' => 2.90, 'high_24h' => 35.80, 'low_24h' => 33.10, 'volume_24h' => 210000.0],
            ['ticker' => 'LINKUSDT', 'name' => 'LINK / USDT', 'current_price' => 18.75, 'change_1d_percentage' => 0.65, 'high_24h' => 19.40, 'low_24h' => 18.20, 'volume_24h' => 180000.0],
            ['ticker' => 'DOTUSDT', 'name' => 'DOT / USDT', 'current_price' => 7.80, 'change_1d_percentage' => -0.45, 'high_24h' => 8.15, 'low_24h' => 7.65, 'volume_24h' => 140000.0],
        ];

        return [
            'status' => 'success',
            'data' => $fallbacks,
            'code' => 200,
        ];
    }

    /**
     * Fallback simulated trades.
     */
    protected function getFallbackTrades(): array
    {
        $now = (int)(microtime(true) * 1000);
        return [
            ['price' => 96520.50, 'qty' => 0.125, 'time' => $now - 2000, 'is_buyer_maker' => false],
            ['price' => 96519.80, 'qty' => 0.450, 'time' => $now - 4000, 'is_buyer_maker' => true],
            ['price' => 96521.00, 'qty' => 0.080, 'time' => $now - 7000, 'is_buyer_maker' => false],
            ['price' => 96522.30, 'qty' => 1.200, 'time' => $now - 11000, 'is_buyer_maker' => false],
            ['price' => 96518.90, 'qty' => 0.650, 'time' => $now - 15000, 'is_buyer_maker' => true],
        ];
    }
}
