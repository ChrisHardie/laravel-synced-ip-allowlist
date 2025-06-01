<?php

namespace ChrisHardie\SyncedIpAllowlist\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\IpUtils;

class RestrictByAllowedIps
{
    public function handle(Request $request, Closure $next)
    {
        // Do not restrict access by IP address in local or test environments
        if (! app()->environment(['local', 'testing'])) {
            // Get the list of allowed CIDRs
            $allowedCidrs = Cache::get(config('synced-ip-allowlist.allowed_ips_cache_key'), []);

            // Determine if requestor IP is in allowed list
            foreach ($request->getClientIps() as $ip) {
                if (! IpUtils::checkIp($ip, $allowedCidrs)) {
                    // If the config has defined a redirect URL, use it
                    if (config('synced-ip-allowlist.unauthorized_redirect_url')) {
                        return redirect(config('synced-ip-allowlist.unauthorized_redirect_url'));
                    }
                    // Otherwise show a 403 unauthorized error
                    abort(403);
                }
            }
        }

        return $next($request);
    }
}
