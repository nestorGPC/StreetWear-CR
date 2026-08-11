<?php

return [

    'base_url' => env(
        'PAYPAL_SANDBOX_BASE_URL',
        'https://api-m.sandbox.paypal.com'
    ),

    'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID'),

    'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET'),

    'exchange_rate' => env('PAYPAL_EXCHANGE_RATE', 520),

];
