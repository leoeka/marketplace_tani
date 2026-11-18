<?php

/*
 * Bahasa disimpan (file lang/): en, id, zh, ar.
 * Bahasa otomatis (Google Cloud Translation): diambil dari katalog config/export_languages.php,
 * aktif hanya jika GOOGLE_TRANSLATE_API_KEY terisi. Pilihan diatur di .env tanpa mengubah kode:
 *   EXPORT_MACHINE_LOCALES=all          semua bahasa katalog (bawaan)
 *   EXPORT_MACHINE_LOCALES=ja,ko,fr     hanya bahasa yang disebut
 *   EXPORT_MACHINE_LOCALES=none         tidak ada bahasa otomatis
 */

$catalog = require __DIR__ . '/export_languages.php';

$stored = [
    'en' => ['lang' => 'en', 'dir' => 'ltr', 'label' => 'English'],
    'id' => ['lang' => 'id', 'dir' => 'ltr', 'label' => 'Bahasa Indonesia'],
    'zh' => ['lang' => 'zh-CN', 'dir' => 'ltr', 'label' => '中文'],
    'ar' => ['lang' => 'ar', 'dir' => 'rtl', 'label' => 'العربية'],
];

$machineEnabled = filter_var(env('EXPORT_MT_ENABLED', true), FILTER_VALIDATE_BOOLEAN)
    && trim((string) env('GOOGLE_TRANSLATE_API_KEY')) !== '';

$setting = strtolower(trim((string) env('EXPORT_MACHINE_LOCALES', 'all')));

$machine = [];
if ($machineEnabled && $setting !== '' && $setting !== 'none') {
    $requested = $setting === 'all'
        ? array_keys($catalog)
        : array_map('trim', explode(',', $setting));

    $machine = array_values(array_filter(
        $requested,
        fn($code) => isset($catalog[$code]) && !isset($stored[$code])
    ));
}

$attributes = $stored;
foreach ($machine as $code) {
    $attributes[$code] = ['lang' => $code, 'dir' => $catalog[$code][1], 'label' => $catalog[$code][0]];
}

return [
    'locales' => array_keys($attributes),
    'stored_locales' => array_keys($stored),
    'machine_locales' => $machine,
    'default_locale' => 'en',
    'rfq_notification_email' => env('ADMIN_EMAIL'),
    'locale_attributes' => $attributes,

    // Mode deteksi bahasa browser: suggest (banner saran), auto (langsung ganti), off.
    'lang_detect' => env('EXPORT_LANG_DETECT', 'suggest'),

    'machine' => [
        'enabled' => $machineEnabled,
        'api_key' => env('GOOGLE_TRANSLATE_API_KEY'),
        // Batas karakter per bulan. Di atas batas ini tidak ada lagi panggilan ke Google (tampil English).
        'monthly_char_limit' => (int) env('GOOGLE_TRANSLATE_MONTHLY_CHAR_LIMIT', 400000),
        // Teks yang TIDAK diterjemahkan mesin (tampil English): merek, istilah dagang, penafian logistik.
        'exclude' => array_merge(
            ['brand', 'rfq.incoterms_options.*', 'logistics.disclaimer', 'products.currency_prefix', 'products.unit_kg'],
            // Nama petani dan nama toko tidak diterjemahkan
            array_map(fn($n) => "products.farmer_{$n}", range(1, 4)),
            array_map(fn($n) => "farmers.farmer_{$n}", range(1, 3)),
            array_map(fn($n) => "farmers.farmer_{$n}_shop", range(1, 3)),
        ),
        'content' => filter_var(env('MARKETPLACE_TRANSLATE_CONTENT', true), FILTER_VALIDATE_BOOLEAN),
    ],

    // Kurs: jumlah mata uang tujuan untuk 1 USD. Diisi dari .env, tanpa angka bawaan.
    // Mata uang yang kursnya kosong tidak ditawarkan kepada pengunjung.
    'default_currency' => 'USD',
    'currencies' => [
        'USD' => ['rate' => 1],
        'EUR' => ['rate' => env('EXPORT_RATE_EUR')],
        'SGD' => ['rate' => env('EXPORT_RATE_SGD')],
    ],
    'rates_as_of' => env('EXPORT_RATES_DATE'),
];