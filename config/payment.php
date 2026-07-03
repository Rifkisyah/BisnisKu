<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Payment Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default payment gateway provider used when
    | the database setting is not explicitly requested. Supported providers
    | are: 'midtrans', 'xendit', 'dana'.
    |
    */
    'default' => env('PAYMENT_DEFAULT_PROVIDER', 'midtrans'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure the credentials for each payment gateway provider.
    | These credentials should be stored in the .env file for security.
    |
    */
    'gateways' => [
        'midtrans' => [
            'merchant_id'   => env('MIDTRANS_MERCHANT_ID'),
            'client_key'    => env('MIDTRANS_CLIENT_KEY'),
            'server_key'    => env('MIDTRANS_SERVER_KEY'),
            'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        ],

        'xendit' => [
            'secret_key'    => env('XENDIT_SECRET_KEY'),
            'public_key'    => env('XENDIT_PUBLIC_KEY'),
        ],

        'dana' => [
            'merchant_id'   => env('DANA_MERCHANT_ID'),
            'client_secret' => env('DANA_CLIENT_SECRET'),
        ],
    ],
];
