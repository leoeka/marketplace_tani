<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Menyimpan pilihan bahasa dashboard (Indonesia / English) pada akun petani atau admin.
 */
class DashboardLocaleController extends Controller
{
    public function set(Request $request, string $locale): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user !== null && in_array($user->role, ['farmer', 'admin'], true), 403);
        abort_unless(array_key_exists($locale, (array) config('dashboard.locales', [])), 404);

        $user->forceFill(['locale' => $locale])->save();

        return back();
    }
}