<?php

// config for ChrisHardie/SyncedIpAllowlist
return [
    // The URL where the encrypted list of IP addresses allowed in CIDR notation is available
    'allowed_ips_url' => env('ALLOWED_IPS_URL', 'https://example.com/allowed-ips.txt'),
    // The encryption key for encrypting and decrypting the list of IP addresses
    'allowed_ips_key' => env('ALLOWED_IPS_KEY', env('APP_KEY')),
    // The cache key used to store the list of allowed IP addresses
    'allowed_ips_cache_key' => 'allowed-ips.cidrs',
    // An optional URL to redirect unauthorized users to instead of showing a 403 error
    'unauthorized_redirect_url' => env('ALLOWED_IPS_REDIRECT_URL'),
];
