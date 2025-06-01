<?php

// config for ChrisHardie/SyncedIpAllowlist
return [
    'allowed_ips_url' => env('ALLOWED_IPS_URL', 'https://example.com/allowed-ips.txt'),
    'allowed_ips_key' => env('ALLOWED_IPS_KEY', env('APP_KEY')),
    'allowed_ips_cache_key' => 'allowed-ips.cidrs',
    'allowed_ips_cache_expire' => 12, // hours
    'unauthorized_redirect_url' => env('APP_URL', 'https://example.com/'),
];
