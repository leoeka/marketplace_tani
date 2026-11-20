<?php

/*
 * Dua website dalam satu aplikasi.
 *   MARKETPLACE_DOMAIN=nusaresource.com
 *   EXPORT_DOMAIN=vienusantaraglobal.com
 * Jika KEDUANYA diisi, aplikasi berjalan "mode dua domain" (website ekspor di akar domainnya).
 * Jika salah satu kosong, website ekspor tetap di alamat /export (mode demo / pengujian).
 */
return [
    'marketplace_domain' => env('MARKETPLACE_DOMAIN'),
    'export_domain' => env('EXPORT_DOMAIN'),
];
