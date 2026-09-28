<?php

// Define website defaults

return [
    // Will be placed in .env later with corresponding keys
    'template' => env("TEMPLATE", 'york'),
    'app_name' => env("APP_NAME", 'Foyana'),
    'favicon' => env("FAVICON", 'favicon.png'),
    'app_timezone' => env("APP_TIMEZONE", 'Europe/London'),
    'decimal_places' => env("DECIMAL_PLACES", 2),
    'use_vite' => env("USE_VITE", true),
    'search_engine_indexing' => env("SEARCH_ENGINE_INDEXING", true),
    'append_date_to_emails' => env("APPEND_DATE_TO_EMAILS", 'enabled'),
    'product_key' => env('PRODUCT_KEY'),
    'setup_server_url' => env('SETUP_SERVER_URL'),
    'version' => env('APP_VERSION', '1.0.0'),

    'settings_defaults' => [
        // Core
        'name' => 'Foyana',
        'logo_square' => 'logo-square.png',
        'logo_rectangle' => 'logo-rectangle.png',
        'favicon' => 'favicon.png',
        'email' => env('MAIL_FROM_ADDRESS', 'admin@example.com'),
        'timezone' => env("APP_TIMEZONE", 'Europe/London'),


        // Security
        'email_verification' => 'enabled',
        'google_recaptcha' => 'disabled',
        'require_strong_password' => 'disabled',
        'login_otp' => 'enabled',

        // Benefits
        'welcome_bonus' => 10,
        'referral_bonus' => [
            10,
            5,
            3,
            0,
            0,
            0,
        ],

        // System
        'pagination' => 10,
        'delete_notification_message' => 'enabled',
        //langauges

        // finance
        'currency' => 'GBP',
        'currency_symbol' => '£',
        'currency_symbol_position' => 'before', //or after
        'decimal_places' => 2,

        // Finance -  deposit
        'min_deposit' => 1,
        'max_deposit' => 6000,
        'deposit_fee' => 5,
        'deposit_expires_at' => 10, //hours

        // Finance -  withdrawal
        'min_withdrawal' => 1,
        'max_withdrawal' => 6000,
        'withdrawal_fee' => 5,


        // Email Notification setting
        'email_notification' => [
            'append_date_to_subject' => 'enabled',
            'email_queue' => 'disabed',
            'notifications' => [
                'deposit' => [
                    'status' => 'enabled',
                    'tip' => 'If this is enabled, users will get email notification when they make deposits or when their deposit status changes.',
                    'warning' => null,
                ],
                'email_verification' => [
                    'status' => 'enabled',
                    'tip' => 'If this is enabled, users will get email notification when they register.',
                    'warning' => "If you have enabled email verification in the security setting and disabled this notification, users won't be able to sign up as no verification link or code will be sent.",
                ],
                'kyc' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when they carry out KYC verification or when their KYC status changes.",
                    'warning' => null,
                ],
                'otp_verification' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when they make attempt any action that requires an OTP code.",
                    'warning' => "If you have enabled OTP verification in the security setting and disabled this notification, users won't be able to login or carryout any action that requires otp verification as no OTP code will be sent.",
                ],
                'referral' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when someone sign up with their referral link or code.",
                    'warning' => null,
                ],
                'transaction' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when any transaction occurs on their account.",
                    'warning' => "Sending too many emails can trigger email quota limit, blacklisting or spam. Consult your hosting provider.",
                ],
                'welcome' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when their sign up is completed.",
                    'warning' => null,
                ],
                'withdrawal' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when they withdraw or when their withdrawal status changes.",
                    'warning' => null,
                ],
                'account_ban' => [
                    'status' => 'enabled',
                    'tip' => "If this is enabled, users will get email notification when their account is banned or unbanned.",
                    'warning' => null,
                ],
            ],
        ],

        // regulatory compliance
        'regulatory_compliance' => [
            'regulators' => [
                'FinCEN Registered Money Services Business (MSB)',
                'Virtual Asset Regulatory Authority (VARA Compliance)',
                'European MiCA (Markets in Crypto-Assets) Framework',
                'Financial Conduct Authority (FCA Standards)',
            ],
            'pdf_certificates' => [
                [
                    'name' => 'Digital Asset Custody & Settlement Certificate',
                    'file' => 'digital_asset_custody_certificate.pdf',
                ],
                [
                    'name' => 'Algorithmic Trading & Technology License',
                    'file' => 'algorithmic_trading_license.pdf',
                ],
                [
                    'name' => 'MSB Financial Operations Certificate',
                    'file' => 'msb_financial_operations_certificate.pdf',
                ],
                [
                    'name' => 'Anti-Money Laundering (AML) Audit Verification',
                    'file' => 'aml_audit_verification.pdf',
                ],
            ],
        ],

        // SEO
        'seo_description' => 'Automate your crypto trading with algorithmic AI bots and mirror verified strategies in real time with copy trading on :site_name. Fast execution, total portfolio control.',
        'seo_keywords' => 'copy trading, AI trading bots, automated crypto trading, algorithmic trading, crypto copy trading, automated trading strategies, quantitative trading, :site_name',
        'social_title' => ':site_name | Automated AI Bot & Copy Trading Platform',
        'social_description' => 'Deploy automated AI trading bots and copy top-performing crypto traders in real time on :site_name. Institutional-grade execution built for modern digital asset traders.',
        'seo_image' => 'seo-banner.png',

        'social_media' => [
            'twitter' => null,
            'facebook' => null,
            'instagram' => null,
            'linkedin' => null,
            'youtube' => null,
            'telegram' => null,
            'whatsapp' => null,
            'tiktok' => null,
            'x' => null,
        ],

    ]
];
