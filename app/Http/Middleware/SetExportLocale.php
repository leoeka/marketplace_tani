<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetExportLocale
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = config('export.locales', []);
        $requestedLocale = $request->session()->get('export_locale');
        $defaultLocale = config('export.default_locale', 'en');
        $locale = is_string($requestedLocale) && in_array($requestedLocale, $locales, true)
            ? $requestedLocale
            : $defaultLocale;

        App::setLocale($locale);

        return $next($request);
    }
}