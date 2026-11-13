<?php

return [
    'locales' => ['en', 'zh', 'ar'],
    'default_locale' => 'en',
    'rfq_notification_email' => env('ADMIN_EMAIL'),
    'locale_attributes' => [
        'en' => ['lang' => 'en', 'dir' => 'ltr', 'label' => 'English'],
        'zh' => ['lang' => 'zh-CN', 'dir' => 'ltr', 'label' => '中文'],
        'ar' => ['lang' => 'ar', 'dir' => 'rtl', 'label' => 'العربية'],
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