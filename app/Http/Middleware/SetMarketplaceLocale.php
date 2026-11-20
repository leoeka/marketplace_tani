<?php

namespace App\Http\Middleware;

use App\Support\MachineTranslator;
use App\Support\Site;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bahasa marketplace (pembeli). Urutan: pilihan manual (session "locale") -> bahasa browser
 * -> English. Tidak pernah memakai lokasi. Berlaku untuk semua halaman kecuali /export
 * (diatur SetExportLocale sendiri).
 *
 * Dashboard petani dan admin (admin*, dashboard-petani*, profile) memakai bahasa pilihan akun
 * (kolom users.locale: id atau en, bawaan id), terpisah dari bahasa marketplace pembeli.
 */
class SetMarketplaceLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Site::isExport($request)) {
            return $next($request);
        }

        $locales = (array) config('export.locales', []);
        $default = (string) config('export.default_locale', 'en');
        $mode = (string) config('export.lang_detect', 'suggest');

        $chosen = $request->session()->get('locale');
        $locale = is_string($chosen) && in_array($chosen, $locales, true) ? $chosen : null;
        $suggested = null;

        if ($locale === null) {
            $detected = $mode === 'off' ? null : $this->detectFromBrowser($request, $locales);

            if ($detected !== null && $detected !== $default) {
                if ($mode === 'auto') {
                    $locale = $detected;
                    $request->session()->put('locale', $detected);
                } elseif ($mode === 'suggest') {
                    $suggested = $detected;
                }
            }
        }

        $active = $locale ?? $default;

        if ($this->isStaffArea($request)) {
            $active = $this->staffLocale($request);
            $suggested = null;
        }

        App::setLocale($active);

        if ($active !== $default) {
            app(MachineTranslator::class)->loadGroups($active, MachineTranslator::MARKETPLACE_GROUPS);
        }

        View::share('suggestedLocale', $suggested);

        return $next($request);
    }

    /** Bahasa dashboard: pilihan akun (users.locale) bila valid, selain itu bawaan dashboard (id). */
    private function staffLocale(Request $request): string
    {
        $allowed = array_keys((array) config('dashboard.locales', ['id' => 'Bahasa Indonesia']));
        $own = $request->user()?->locale;

        return is_string($own) && in_array($own, $allowed, true)
            ? $own
            : (string) config('dashboard.default_locale', 'id');
    }

    /** Area dashboard petani/admin (dan profil mereka): bahasa mengikuti pilihan akun. */
    private function isStaffArea(Request $request): bool
    {
        $user = $request->user();

        if ($user === null || ! in_array($user->role, ['farmer', 'admin'], true)) {
            return false;
        }

        return $request->is('admin', 'admin/*', 'dashboard-petani', 'dashboard-petani/*', 'profile');
    }

    private function detectFromBrowser(Request $request, array $locales): ?string
    {
        if ($locales === [] || $request->header('Accept-Language') === null) {
            return null;
        }

        $match = $request->getPreferredLanguage($locales);

        return is_string($match) && in_array($match, $locales, true) ? $match : null;
    }
}