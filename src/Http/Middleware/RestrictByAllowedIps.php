<?php

namespace ChrisHardie\SyncedIpAllowlist\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\IpUtils;

class RestrictByAllowedIps
{
    public function handle(Request $request, Closure $next)
    {
        if (! app()->environment(['local', 'testing'])) {
            $allowedCidrs = Cache::get(config('synced-ip-allowlist.allowed_ips_cache_key'), []);

            foreach ($request->getClientIps() as $ip) {
                if (! IpUtils::checkIp($ip, $allowedCidrs)) {
                    if (config('synced-ip-allowlist.unauthorized_redirect_url')) {
                        return redirect(config('synced-ip-allowlist.unauthorized_redirect_url'));
                    }
                    abort(403);
                }
            }
        }

        return $next($request);
    }
}
