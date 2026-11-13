<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use App\Support\ExportCurrency;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExportPreferenceController extends Controller
{
    public function locale(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, (array) config('export.locales', []), true), 404);

        $request->session()->put('export_locale', $locale);

        return $this->backToExport($request);
    }

    public function currency(Request $request, string $currency): RedirectResponse
    {
        $currency = strtoupper($currency);

        abort_unless(in_array($currency, ExportCurrency::available(), true), 404);

        $request->session()->put('export_currency', $currency);

        return $this->backToExport($request);
    }

    /**
     * Kembali ke halaman ekspor sebelumnya (termasuk query kalkulator).
     * Alamat di luar website ekspor atau domain lain diabaikan.
     */
    private function backToExport(Request $request): RedirectResponse
    {
        $fallback = route('export.home');
        $previous = url()->previous($fallback);
        $parts = parse_url($previous) ?: [];
        $path = $parts['path'] ?? '/';

        $sameHost = ! isset($parts['host']) || $parts['host'] === $request->getHost();
        $insideExport = str_starts_with($path, '/export');
        $isPreferenceUrl = str_contains($path, '/export/locale/') || str_contains($path, '/export/currency/');

        return redirect()->to($sameHost && $insideExport && ! $isPreferenceUrl ? $previous : $fallback);
    }
}