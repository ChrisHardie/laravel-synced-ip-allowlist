<?php

namespace ChrisHardie\SyncedIpAllowlist\Commands;

use Illuminate\Console\Command;

class SyncedIpAllowlistCommand extends Command
{
    public $signature = 'laravel-synced-ip-allowlist';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
