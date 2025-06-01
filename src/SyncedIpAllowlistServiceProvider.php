<?php

namespace ChrisHardie\SyncedIpAllowlist;

use ChrisHardie\SyncedIpAllowlist\Commands\EncryptIpAllowlistCommand;
use Illuminate\Console\Scheduling\Schedule;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ChrisHardie\SyncedIpAllowlist\Commands\SyncedIpAllowlistCommand;

class SyncedIpAllowlistServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-synced-ip-allowlist')
            ->hasConfigFile()
            ->hasCommands([
                SyncedIpAllowlistCommand::class,
                EncryptIpAllowlistCommand::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->scheduleIpSync();
    }

    protected function scheduleIpSync(): void
    {
        $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('ip-allowlist:sync')->twiceDaily();
        });
    }
}
