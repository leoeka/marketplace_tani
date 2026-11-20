<?php

namespace App\Support;

use Illuminate\Http\Request;

class Site
{
    public static function marketplaceDomain(): ?string
    {
        return self::clean(config('sites.marketplace_domain'));
    }

    public static function exportDomain(): ?string
    {
        return self::clean(config('sites.export_domain'));
    }

    /** Mode dua domain aktif bila kedua domain diisi. */
    public static function split(): bool
    {
        return self::marketplaceDomain() !== null && self::exportDomain() !== null;
    }

    /** Apakah permintaan ini untuk website ekspor? */
    public static function isExport(Request $request): bool
    {
        if (self::split()) {
            return strtolower($request->getHost()) === self::exportDomain();
        }

        return $request->is('export', 'export/*');
    }

    private static function clean(mixed $value): ?string
    {
        $value = is_string($value) ? strtolower(trim($value)) : '';
        $value = preg_replace('#^https?://#', '', $value) ?? '';
        $value = rtrim($value, '/');

        return $value === '' ? null : $value;
    }
}
