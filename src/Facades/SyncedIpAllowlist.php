<?php

namespace ChrisHardie\SyncedIpAllowlist\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ChrisHardie\SyncedIpAllowlist\SyncedIpAllowlist
 */
class SyncedIpAllowlist extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ChrisHardie\SyncedIpAllowlist\SyncedIpAllowlist::class;
    }
}
