<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteLocaleController extends Controller
{
    /** Menyimpan pilihan bahasa marketplace pengunjung di sesi, lalu kembali ke halaman sebelumnya. */
    public function set(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, (array) config('export.locales', []), true), 404);

        $request->session()->put('locale', $locale);

        return redirect()->back(fallback: route('home'));
    }
}