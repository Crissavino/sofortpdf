<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment bypass
    |--------------------------------------------------------------------------
    |
    | When the request qualifies for bypass, the paywall gates are
    | short-circuited:
    |   - No login required to use a tool
    |   - No subscription check
    |   - Download tokens land in cache instead of the downloads table
    |     (avoids the shared-DB customer_id FK)
    |
    | A request qualifies when *any* of these is true:
    |   - PAYMENT_BYPASS=true            → bypass for everyone
    |   - PAYMENT_BYPASS_IPS contains the request IP (CSV, supports
    |     CIDR like 192.168.0.0/24)
    |
    | This is meant for the period before the payment-popup modal
    | (à la conversie-pdf / contract-kit) ships. Drop both vars the
    | moment that modal is wired up.
    */
    'payment_bypass' => env('PAYMENT_BYPASS', false),

    // CSV of IPs / CIDR blocks that get bypass even when
    // PAYMENT_BYPASS=false. Empty means no per-IP allowlist.
    'payment_bypass_ips' => array_values(array_filter(array_map('trim', explode(',', (string) env('PAYMENT_BYPASS_IPS', ''))))),

    // TTL (hours) for cache-based download tokens used while bypassed.
    'guest_download_ttl_hours' => (int) env('GUEST_DOWNLOAD_TTL_HOURS', 4),
];
