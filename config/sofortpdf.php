<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment bypass
    |--------------------------------------------------------------------------
    |
    | When true, every paywall gate is short-circuited:
    |   - No login required to use a tool
    |   - No subscription check
    |   - The download token is stored in cache instead of the downloads
    |     table so we don't depend on the shared customer_id FK
    |
    | This is meant for the period before the payment-popup modal
    | (à la conversie-pdf / contract-kit) ships. Flip back to false
    | the moment that modal is wired up.
    */
    'payment_bypass' => env('PAYMENT_BYPASS', false),

    // TTL (hours) for cache-based download tokens used while bypassed.
    'guest_download_ttl_hours' => (int) env('GUEST_DOWNLOAD_TTL_HOURS', 4),
];
