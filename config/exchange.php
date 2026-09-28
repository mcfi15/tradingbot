<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Supported exchanges
    |--------------------------------------------------------------------------
    |
    | Keys of this array are the canonical exchange identifiers persisted in
    | the database (exchange_connections.exchange). Each key must resolve to a
    | class implementing App\Services\Exchange\ExchangeServiceInterface via
    | the "adapters" map below.
    |
    */

    'supported' => ['binance', 'bybit', 'mexc'],

    /*
    |--------------------------------------------------------------------------
    | Adapter class map
    |--------------------------------------------------------------------------
    */

    'adapters' => [
        'binance' => App\Services\Exchange\BinanceService::class,
        'bybit' => App\Services\Exchange\BybitService::class,
        'mexc' => App\Services\Exchange\MexcService::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTP client tuning
    |--------------------------------------------------------------------------
    |
    | "verify" should stay true in production. It is only relaxed when the
    | explicit EXCHANGE_VERIFY_TLS=false flag is set, because exchanges are
    | public HTTPS APIs and certificate validation must not be silently off.
    |
    */

    'http' => [
        'timeout' => (int) env('EXCHANGE_HTTP_TIMEOUT', 15),
        'connect_timeout' => (int) env('EXCHANGE_CONNECT_TIMEOUT', 8),
        'verify' => filter_var(env('EXCHANGE_VERIFY_TLS', true), FILTER_VALIDATE_BOOLEAN),
    ],

    /*
    |--------------------------------------------------------------------------
    | Balance cache TTL
    |--------------------------------------------------------------------------
    |
    | Exchange balance endpoints are weight-heavy and rate limited. Values are
    | cached per user + connection + market so a dashboard refresh never
    | triggers a live exchange call.
    |
    */

    'balance_cache_ttl' => (int) env('EXCHANGE_BALANCE_CACHE_TTL', 60),

    /*
    |--------------------------------------------------------------------------
    | Quote asset used to total a wallet
    |--------------------------------------------------------------------------
    */

    'quote_assets' => ['USDT', 'USDC', 'BUSD'],

    /*
    |--------------------------------------------------------------------------
    | Queue names
    |--------------------------------------------------------------------------
    |
    | "trades" is consumed by the order execution worker. Keep it separate from
    | "balances" so a burst of signals can never starve balance refreshes.
    |
    */

    'queues' => [
        'trades' => env('EXCHANGE_TRADE_QUEUE', 'trades'),
        'balances' => env('EXCHANGE_BALANCE_QUEUE', 'balances'),
    ],
];
