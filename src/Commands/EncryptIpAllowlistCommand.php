<?php

namespace ChrisHardie\SyncedIpAllowlist\Commands;

use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Str;

class EncryptIpAllowlistCommand extends Command
{
    protected $signature = 'ip-allowlist:encrypt
                            {--key= : Base64-encoded encryption key (32 bytes)}';

    protected $description = 'Prompt for IPs and output encrypted blob';

    public function handle(): int
    {
        $key = $this->option('key') ?: config('synced-ip-allowlist.allowed_ips_key');

        if (! $key || ! Str::startsWith($key, 'base64:')) {
            $this->error('Missing or invalid base64 key. Use --key=base64:...');
            return self::FAILURE;
        }

        $decodedKey = base64_decode(Str::after($key, 'base64:'), true);

        if (! $decodedKey || strlen($decodedKey) !== 32) {
            $this->error('Invalid key. Must be base64-encoded 32-byte string.');
            return self::FAILURE;
        }

        $this->info("Enter allowed IPs/ranges (one per line). Finish input with CTRL+D (Linux/macOS) or CTRL+Z (Windows):");

        $handle = fopen('php://stdin', 'rb');
        $input = '';

        while (($line = fgets($handle)) !== false) {
            $input .= $line;
        }

        fclose($handle);

        $plaintext = trim($input);

        $encrypter = new Encrypter($decodedKey, 'AES-256-CBC');
        $encrypted = $encrypter->encryptString($plaintext);

        $this->info('Encrypted IP list:');
        $this->line($encrypted);

        return Command::SUCCESS;
    }
}
