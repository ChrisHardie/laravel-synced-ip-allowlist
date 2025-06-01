<?php

namespace ChrisHardie\SyncedIpAllowlist\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SyncedIpAllowlistCommand extends Command
{
    public $signature = 'ip-allowlist:sync';

    public $description = 'Sync allowlist IPs from source';

    public function handle(): int
    {
        $url = config('synced-ip-allowlist.allowed_ips_url');

        $this->info("Fetching IP list from $url");

        try {
            $response = Http::timeout(5)->get($url);

            if (! $response->successful()) {
                $this->error('Failed to fetch list: ' . $response->body());
                return self::FAILURE;
            }
        } catch (ConnectionException $e) {
            $this->error('Failed to connect to $url: ' . $e->getMessage());
            return self::FAILURE;
        }

        // Decrypt the IP addresses
        $base64Key = config('synced-ip-allowlist.allowed_ips_key');
        $decodedKey = base64_decode(Str::after($base64Key, 'base64:'), true);
        if (! $decodedKey || strlen($decodedKey) !== 32) {
            throw new \RuntimeException('Invalid key length for AES-256-CBC');
        }
        $encrypter = new Encrypter($decodedKey, 'AES-256-CBC');
        $plaintext = $encrypter->decryptString($response->body());

        // Filter out comments and blank lines
        $cidrs = collect(explode("\n", $plaintext))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line && ! str_starts_with($line, '#'))
            ->values()
            ->all();

        // Store the CIDRs in the cache for use by the middleware
        Cache::put(
            config('synced-ip-allowlist.allowed_ips_cache_key'),
            $cidrs
        );

        $this->info("Cached " . count($cidrs) . " CIDRs.");
        return self::SUCCESS;
    }
}
