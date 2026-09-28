<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Broadcaster
    |--------------------------------------------------------------------------
    |
    | "reverb" is the default once laravel/reverb is installed. The "log" and
    | "null" drivers are kept as safe fallbacks so a broadcast dispatched on a
    | host without Reverb running degrades to a no-op instead of throwing.
    |
    */

    'default' => env('BROADCAST_CONNECTION', 'reverb'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Connections
    |--------------------------------------------------------------------------
    */

    'connections' => [

        'reverb' => [
            'driver' => 'reverb',
            'key' => env('REVERB_APP_KEY'),
            'secret' => env('REVERB_APP_SECRET'),
            'app_id' => env('REVERB_APP_ID'),
            'options' => [
                'host' => env('REVERB_HOST'),
                'port' => env('REVERB_PORT', 443),
                'scheme' => env('REVERB_SCHEME', 'https'),
                'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
            ],
            // Client options are exposed to the browser through /broadcasting/auth
            // and are what Laravel Echo reads on the frontend.
            'client_options' => [
                // Reverb 1.x
                'broadcaster' => 'reverb',
                'key' => env('REVERB_APP_KEY'),
                'wsHost' => env('REVERB_HOST'),
                'wsPort' => env('REVERB_PORT', 443),
                'wssPort' => env('REVERB_PORT', 443),
                'forceTLS' => (bool) env('REVERB_SCHEME', 'https') === 'https',
                'enabledTransports' => ['ws', 'wss'],
            ],
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'host' => env('PUSHER_HOST') ?: 'api-' . env('PUSHER_APP_CLUSTER', 'mt1') . '.pusher.com',
                'port' => env('PUSHER_PORT', 443),
                'scheme' => env('PUSHER_SCHEME', 'https'),
                'useTLS' => env('PUSHER_SCHEME', 'https') === 'https',
            ],
        ],

        'log' => [
            'driver' => 'log',
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

];
