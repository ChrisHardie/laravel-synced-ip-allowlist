# Laravel Synced IP Allowlist

[![Latest Version on Packagist](https://img.shields.io/packagist/v/chrishardie/laravel-synced-ip-allowlist.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-synced-ip-allowlist)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/chrishardie/laravel-synced-ip-allowlist/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/chrishardie/laravel-synced-ip-allowlist/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/chrishardie/laravel-synced-ip-allowlist/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/chrishardie/laravel-synced-ip-allowlist/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/chrishardie/laravel-synced-ip-allowlist.svg?style=flat-square)](https://packagist.org/packages/chrishardie/laravel-synced-ip-allowlist)

A Laravel package that provides HTTP middleware to allow access from a centralized list of IPs

## Installation

You can install the package via composer:

```bash
composer require chrishardie/laravel-synced-ip-allowlist
```

**Set Up Variables**

If you haven't already, generate an encryption key to use across all apps retrieving the list of IP addresses.

```bash
php artisan key:generate --show
base64:...
```

Copy the key to a secure location and store it in the `ALLOWED_IPS_KEY` environment variable, see below.

Open `.env` and define these variables:

```bash
# The URL where the encrypted list of allowed IPs is publicized
ALLOWED_IPS_URL="https://example.com/allowed-ips.txt"
# Encryption key for securely publicizing the IP addresses allowed
ALLOWED_IPS_KEY="base64:..."
# An optional URL to redirect unauthorized users to
ALLOWED_IPS_REDIRECT_URL="https://laravel.com/"
```

If you want to further change package behavior, you can optionally publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-synced-ip-allowlist-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

### Encrypting and Publicizing Allowed IPs

Encrypt your list of IP addresses:

```bash
php artisan artisan ip-allowlist:encrypt
```

Paste or type in the list of IPs and get the encrypted result:

```
Enter allowed IPs/ranges (one per line). Finish input with CTRL+D (Linux/macOS) or CTRL+Z (Windows):
# My first IP
12.34.56.78/32
# My second IP
98.76.54.32/32
Encrypted IP list:
eyJpd...
```

Take the encrypted result and put it at a URL that will be accessible to the applications using this package, e.g. `https://example.com/allowed-ips.txt`

### Run Initial Sync

```bash
php artisan ip-allowlist:sync
```

Result:

```
Fetching IP list from https://example.com/allowed-ips.txt
Cached 2 CIDRs.
```

### Future Syncs are Scheduled

The sync process will run twice daily:

```bash
$ php artisan schedule:list | grep ip-allowlist
  0    1,13  *  * *        php artisan ip-allowlist:sync ..... Next Due: 11 hours from now
```

### Use the Middleware to Protect a Route

In `app/Http/Kernel.php`, add an entry to the list of named, available HTTP route middleware:

```php
protected $routeMiddleware = [
    'allowed-ips' => \ChrisHardie\SyncedIpAllowlist\Http\Middleware\RestrictByAllowedIps::class,
];
```

Then, in your routes file `routes/web.php`:

```php
Route::middleware(['allowed-ips'])->group(function () {
    // Protected routes
    Route::get(...);
    Route::post(...);
});
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Chris Hardie](https://github.com/ChrisHardie)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
