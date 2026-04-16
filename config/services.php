<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'conversion' => [
        'url' => env('CONVERSION_SERVICE_URL', 'http://localhost:8001'),
        'token' => env('CONVERSION_SERVICE_TOKEN', ''),
        'enabled' => env('CONVERSION_SERVICE_ENABLED', false),
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        'trial_price_id' => env('STRIPE_TRIAL_PRICE_ID'),
        'subscription_price_id' => env('STRIPE_SUBSCRIPTION_PRICE_ID'),
        'trial_price' => env('TRIAL_PRICE_EUR', 1.50),
        'trial_days' => env('TRIAL_DAYS', 2),
        'subscription_price' => env('SUBSCRIPTION_PRICE_EUR', 39.99),
    ],

    'bo' => [
        'website_id' => env('WEBSITE_ID'),
    ],

    'ipdata' => [
        'key' => env('IPDATA_KEY'),
    ],

    'ipinfo' => [
        'key' => env('IPINFO_KEY'),
    ],

];
