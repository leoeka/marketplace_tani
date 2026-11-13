<?php

namespace App\Support;

class ExportCurrency
{
    /**
     * Kode mata uang yang memiliki kurs valid di konfigurasi. USD selalu ada.
     *
     * @return array<int, string>
     */
    public static function available(): array
    {
        return array_values(array_filter(
            array_map('strval', array_keys((array) config('export.currencies', []))),
            fn (string $code): bool => self::rate($code) !== null,
        ));
    }

    public static function rate(string $code): ?float
    {
        $rate = config('export.currencies.'.$code.'.rate');

        return is_numeric($rate) && (float) $rate > 0 ? (float) $rate : null;
    }

    public static function current(): string
    {
        $selected = session('export_currency');

        if (is_string($selected) && in_array($selected, self::available(), true)) {
            return $selected;
        }

        return (string) config('export.default_currency', 'USD');
    }

    /**
     * Mengonversi nominal USD yang sudah berformat (contoh "1,250.00")
     * ke mata uang yang dipilih pengunjung.
     *
     * @return array{currency: string, amount: string, rate_label: string}
     */
    public static function fromUsd(string $usdAmount): array
    {
        $usd = (float) str_replace([',', ' '], '', $usdAmount);
        $currency = self::current();
        $rate = self::rate($currency);

        if ($rate === null) {
            $currency = 'USD';
            $rate = 1.0;
        }

        return [
            'currency' => $currency,
            'amount' => number_format($usd * $rate, 2),
            'rate_label' => rtrim(rtrim(number_format($rate, 4, '.', ''), '0'), '.'),
        ];
    }
}