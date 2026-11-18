<?php

namespace App\Http\Middleware;

use App\Support\MachineTranslator;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class SetExportLocale
{
    /**
     * Urutan: pilihan manual (session) -> bahasa browser -> bahasa bawaan (English).
     * Mode deteksi (EXPORT_LANG_DETECT): suggest (banner saran), auto (langsung ganti), off.
     * Bahasa otomatis (Google) dimuat dari cache terjemahan saat dipakai.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locales = (array) config('export.locales', []);
        $default = (string) config('export.default_locale', 'en');
        $mode = (string) config('export.lang_detect', 'suggest');

        $chosen = $request->session()->get('export_locale');
        $locale = is_string($chosen) && in_array($chosen, $locales, true) ? $chosen : null;
        $suggested = null;

        if ($locale === null) {
            $detected = $mode === 'off' ? null : $this->detectFromBrowser($request, $locales);

            if ($detected !== null && $detected !== $default) {
                if ($mode === 'auto') {
                    $locale = $detected;
                    $request->session()->put('export_locale', $detected);
                } elseif ($mode === 'suggest') {
                    $suggested = $detected;
                }
            }
        }

        $active = $locale ?? $default;
        App::setLocale($active);

        if (MachineTranslator::isMachineLocale($active)) {
            app(MachineTranslator::class)->load($active);
        }

        // Dipakai komponen banner saran; null berarti banner tidak ditampilkan.
        View::share('suggestedLocale', $suggested);

        return $next($request);
    }

    /** Bahasa browser pengunjung yang cocok dengan bahasa yang disediakan, atau null. */
    private function detectFromBrowser(Request $request, array $locales): ?string
    {
        if ($locales === [] || $request->header('Accept-Language') === null) {
            return null;
        }

        $match = $request->getPreferredLanguage($locales);

        return is_string($match) && in_array($match, $locales, true) ? $match : null;
    }
}