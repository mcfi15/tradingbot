<?php

return [
    'header_nav' => [
        [
            'name' => 'Home',
            'route_name' => 'home',
            'link' => null,
            'type' => 'link',
            'is_external' => false,
            'is_active' => true
        ],
        [
            'name' => 'Trading Bots',
            'route_name' => 'trading-bots',
            'link' => null,
            'type' => 'link',
            'is_external' => false,
            'is_active' => moduleEnabledFromJson('trading_bot_module')
        ],
        [
            'name' => 'Copy Trading',
            'route_name' => 'copy-trading',
            'link' => null,
            'type' => 'link',
            'is_external' => false,
            'is_active' => moduleEnabledFromJson('copy_trading_module')
        ],
        [
            'name' => 'Company',
            'route_name' => null,
            'link' => '#',
            'type' => 'dropdown',
            'sub_menu' => [
                [
                    'name' => 'About',
                    'route_name' => 'about',
                    'link' => null,
                    'is_external' => false,
                    'is_active' => true
                ],
                [
                    'name' => 'License & Regulation',
                    'route_name' => 'license',
                    'link' => null,
                    'is_external' => false,
                    'is_active' => true
                ],
                [
                    'name' => 'Contact',
                    'route_name' => 'contact',
                    'link' => null,
                    'is_external' => false,
                    'is_active' => true
                ],
            ],
            'is_external' => false,
            'is_active' => true
        ],
    ],

    'footer_nav' => [
        [
            'name' => 'Trading',
            'items' => [
                ['name' => 'Trading Bots', 'route_name' => 'trading-bots', 'is_active' => moduleEnabledFromJson('trading_bot_module')],
                ['name' => 'Copy Trading', 'route_name' => 'copy-trading', 'is_active' => moduleEnabledFromJson('copy_trading_module')],
            ],
            'is_active' => moduleEnabledFromJson('trading_bot_module') || moduleEnabledFromJson('copy_trading_module')
        ],
        [
            'name' => 'Governance',
            'items' => [
                ['name' => 'About Us', 'route_name' => 'about', 'is_active' => true],
                ['name' => 'Contact', 'route_name' => 'contact', 'is_active' => true],
                ['name' => 'License & Regulation', 'route_name' => 'license', 'is_active' => true],
                ['name' => 'Privacy Policy', 'route_name' => 'privacy-policy', 'is_active' => true],
                ['name' => 'Terms of Service', 'route_name' => 'terms-and-conditions', 'is_active' => true],
                ['name' => 'Risk Disclosure', 'route_name' => 'risk-disclosure', 'is_active' => true],
            ],
            'is_active' => true
        ],
    ]

];