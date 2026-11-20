<?php

namespace App\Http\Middleware;

use App\Support\Site;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Mode dua domain: www.nusaresource.com -> nusaresource.com (dan sama untuk domain ekspor). */
class RedirectWwwHost
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Site::split()) {
            $host = strtolower($request->getHost());

            if (str_starts_with($host, 'www.')) {
                $bare = substr($host, 4);

                if (in_array($bare, [Site::marketplaceDomain(), Site::exportDomain()], true)) {
                    return redirect()->to($request->getScheme() . '://' . $bare . $request->getRequestUri(), 301);
                }
            }
        }

        return $next($request);
    }
}
