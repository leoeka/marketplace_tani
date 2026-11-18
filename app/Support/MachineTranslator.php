<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Terjemahan otomatis (Google Cloud Translation v2) untuk website ekspor.
 * Sumber selalu English (lang/en). Hasil disimpan di tabel machine_translations,
 * pemakaian karakter dicatat per bulan dan dibatasi oleh GOOGLE_TRANSLATE_MONTHLY_CHAR_LIMIT.
 * Jika Google gagal, kunci kosong, atau batas habis, teks tampil dalam English.
 */
class MachineTranslator
{
    private const ENDPOINT = 'https://translation.googleapis.com/language/translate/v2';

    private const BATCH_SIZE = 100;

    private const PLACEHOLDER = '/:[A-Za-z_][A-Za-z0-9_]*/';

    public static function isMachineLocale(string $locale): bool
    {
        return in_array($locale, (array) config('export.machine_locales', []), true);
    }

    /** Grup teks marketplace yang ikut diterjemahkan (sumber: lang/en). */
    public const MARKETPLACE_GROUPS = ['marketplace', 'auth-ui'];

    /**
     * Apakah sebuah grup untuk bahasa ini harus diterjemahkan Google.
     * Ya untuk bahasa otomatis, dan juga untuk bahasa tersimpan (mis. zh, ar) selama file
     * lang/{bahasa}/{grup}.php belum ada. Begitu file itu ditambahkan, otomatis dipakai file tersebut.
     */
    public static function groupNeedsMachine(string $locale, string $group): bool
    {
        if ($locale === (string) config('export.default_locale', 'en')) {
            return false;
        }

        if (self::isMachineLocale($locale)) {
            return true;
        }

        return in_array($locale, (array) config('export.stored_locales', []), true)
            && !is_file(lang_path("{$locale}/{$group}.php"));
    }

    /** Memasang beberapa grup sekaligus (yang perlu diterjemahkan mesin) untuk satu bahasa. */
    public function loadGroups(string $locale, array $groups): void
    {
        foreach ($groups as $group) {
            if (self::groupNeedsMachine($locale, $group)) {
                $this->load($locale, $group);
            }
        }
    }

    /** Bahasa asli isi dari database (nama produk, deskripsi, cerita petani). */
    public const CONTENT_SOURCE = 'id';

    /** Hasil terjemahan isi database selama satu permintaan. */
    private static array $contentMemo = [];

    public static function flushMemo(): void
    {
        self::$contentMemo = [];
    }

    private function hashFor(string $text, string $source): string
    {
        return sha1($source === 'en' ? $text : $source . '|' . $text);
    }

    public function contentEnabled(string $locale): bool
    {
        return $locale !== self::CONTENT_SOURCE
            && (bool) config('export.machine.content', true)
            && $this->enabled();
    }

    /** Menerjemahkan banyak teks isi database sekaligus (satu panggilan Google), lalu mengingatnya. */
    public function primeContent(iterable $texts, string $locale): void
    {
        if (!$this->contentEnabled($locale)) {
            return;
        }

        $todo = [];
        foreach ($texts as $text) {
            if (is_string($text) && trim($text) !== '' && !isset(self::$contentMemo[$locale][$text])) {
                $todo[$text] = $text;
            }
        }

        if ($todo === []) {
            return;
        }

        $map = $this->translateMany(array_values($todo), $locale, self::CONTENT_SOURCE);

        foreach ($todo as $text) {
            self::$contentMemo[$locale][$text] = $map[$text] ?? $text;
        }
    }

    /** Satu teks isi database dalam bahasa pengunjung. Gagal atau mati berarti teks aslinya. */
    public function content(?string $text, ?string $locale = null): string
    {
        $text = (string) $text;
        $locale ??= app()->getLocale();

        if (trim($text) === '' || !$this->contentEnabled($locale)) {
            return $text;
        }

        if (!isset(self::$contentMemo[$locale][$text])) {
            $this->primeContent([$text], $locale);
        }

        return self::$contentMemo[$locale][$text] ?? $text;
    }

    public function enabled(): bool
    {
        return (bool) config('export.machine.enabled', false)
            && trim((string) config('export.machine.api_key')) !== '';
    }

    /** Memasang terjemahan satu grup (mis. "export") untuk bahasa otomatis ke translator Laravel. */
    public function load(string $locale, string $group = 'export'): void
    {
        $translator = app('translator');

        foreach ($this->buildTree($locale, $group) as $key => $value) {
            // Satu entri per kunci teratas, supaya kunci bertitik di dalamnya (mis. "mode.required") utuh.
            $translator->addLines(["{$group}.{$key}" => $value], $locale);
        }
    }

    /** Pohon terjemahan (struktur sama dengan lang/en/{group}.php). Gagal berarti English. */
    public function buildTree(string $locale, string $group = 'export'): array
    {
        $english = (array) app('translator')->getLoader()->load('en', $group);

        $leaves = [];
        $this->collect($english, '', $leaves);

        $exclude = (array) config('export.machine.exclude', []);
        $translatable = array_values(array_unique(array_filter(
            $leaves,
            fn($text, $path) => !Str::is($exclude, $path),
            ARRAY_FILTER_USE_BOTH
        )));

        $map = $this->translateMany($translatable, $locale);

        return $this->rebuild($english, '', $exclude, $map);
    }

    /** Menerjemahkan satu teks English (mis. untuk banner). Jika gagal, mengembalikan teks asli. */
    public function text(string $english, string $locale, array $replace = []): string
    {
        $result = $this->translateMany([$english], $locale)[$english] ?? $english;

        foreach ($replace as $key => $value) {
            $result = str_replace(':' . $key, (string) $value, $result);
        }

        return $result;
    }

    public function usedThisMonth(): int
    {
        try {
            return (int) DB::table('machine_translation_usages')->where('period', now()->format('Y-m'))->value('characters');
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * @param  list<string>  $strings
     * @return array<string,string> teks English => terjemahan (hanya yang berhasil)
     */
    public function translateMany(array $strings, string $locale, string $source = 'en'): array
    {
        $strings = array_values(array_unique(array_filter($strings, fn($s) => is_string($s) && trim($s) !== '')));

        if ($strings === [] || !$this->enabled()) {
            return [];
        }

        try {
            $items = array_map(fn(string $s) => ['text' => $s, 'hash' => $this->hashFor($s, $source)], $strings);
            $result = [];

            foreach (array_chunk($items, 200) as $chunk) {
                $rows = DB::table('machine_translations')
                    ->where('locale', $locale)
                    ->whereIn('source_hash', array_column($chunk, 'hash'))
                    ->pluck('text', 'source_hash');

                foreach ($chunk as $item) {
                    if (isset($rows[$item['hash']])) {
                        $result[$item['text']] = $rows[$item['hash']];
                    }
                }
            }

            $missing = array_values(array_filter($items, fn($item) => !isset($result[$item['text']])));

            return $missing === [] ? $result : $result + $this->fetch($missing, $locale, $source);
        } catch (Throwable $e) {
            Log::warning('Terjemahan otomatis dilewati: ' . $e::class);

            return [];
        }
    }

    /**
     * @param  list<array{text:string,hash:string}>  $missing
     * @return array<string,string>
     */
    private function fetch(array $missing, string $locale, string $source = 'en'): array
    {
        $failKey = "machine-translation:failed:{$locale}";

        if (Cache::has($failKey)) {
            return [];
        }

        $out = [];

        foreach (array_chunk($missing, self::BATCH_SIZE) as $batch) {
            $protected = array_map(fn($item) => $this->protect($item['text']), $batch);
            $chars = array_sum(array_map('mb_strlen', $protected));

            if ($this->usedThisMonth() + $chars > (int) config('export.machine.monthly_char_limit', 400000)) {
                Log::warning('Batas karakter terjemahan bulanan tercapai; tampil English.');
                break;
            }

            try {
                $response = Http::withQueryParameters(['key' => config('export.machine.api_key')])
                    ->timeout(15)
                    ->asJson()
                    ->acceptJson()
                    ->post(self::ENDPOINT, [
                        'q' => $protected,
                        'source' => $source,
                        'target' => $locale,
                        'format' => 'html',
                    ]);

                $translations = $response->successful() ? $response->json('data.translations') : null;

                if (!is_array($translations) || count($translations) !== count($batch)) {
                    throw new \RuntimeException('Respons Google tidak valid (status ' . $response->status() . ').');
                }
            } catch (Throwable $e) {
                // Pesan sengaja tidak memuat URL agar kunci API tidak masuk ke log.
                Log::warning('Panggilan Google Translate gagal: ' . class_basename($e));
                Cache::put($failKey, true, 300);
                break;
            }

            $this->addUsage($chars);

            $rows = [];
            foreach ($batch as $i => $item) {
                $translated = $this->unprotect((string) ($translations[$i]['translatedText'] ?? ''));

                if ($translated === '' || !$this->keepsPlaceholders($item['text'], $translated)) {
                    continue;
                }

                $out[$item['text']] = $translated;
                $rows[] = [
                    'locale' => $locale,
                    'source_hash' => $item['hash'],
                    'text' => $translated,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if ($rows !== []) {
                DB::table('machine_translations')->insertOrIgnore($rows);
            }
        }

        return $out;
    }

    private function collect(array $tree, string $prefix, array &$leaves): void
    {
        foreach ($tree as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $this->collect($value, $path, $leaves);
            } elseif (is_string($value)) {
                $leaves[$path] = $value;
            }
        }
    }

    private function rebuild(array $tree, string $prefix, array $exclude, array $map): array
    {
        foreach ($tree as $key => $value) {
            $path = $prefix === '' ? (string) $key : $prefix . '.' . $key;

            if (is_array($value)) {
                $tree[$key] = $this->rebuild($value, $path, $exclude, $map);
            } elseif (is_string($value) && !Str::is($exclude, $path) && isset($map[$value])) {
                $tree[$key] = $map[$value];
            }
        }

        return $tree;
    }

    /** Placeholder seperti :name dibungkus agar tidak ikut diterjemahkan. */
    private function protect(string $text): string
    {
        $escaped = nl2br(htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8'), false);

        return (string) preg_replace_callback(
            self::PLACEHOLDER,
            fn($m) => '<span translate="no">' . $m[0] . '</span>',
            $escaped
        );
    }

    private function unprotect(string $translated): string
    {
        $plain = (string) preg_replace('#</?span[^>]*>#i', '', $translated);
        $plain = (string) preg_replace('#<br\s*/?>(\r\n|\n|\r)?#i', "\n", $plain);

        return html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function keepsPlaceholders(string $source, string $translated): bool
    {
        preg_match_all(self::PLACEHOLDER, $source, $matches);

        foreach ($matches[0] as $placeholder) {
            if (!str_contains($translated, $placeholder)) {
                return false;
            }
        }

        return true;
    }

    private function addUsage(int $chars): void
    {
        $period = now()->format('Y-m');

        DB::table('machine_translation_usages')->insertOrIgnore([
            'period' => $period,
            'characters' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('machine_translation_usages')->where('period', $period)->increment('characters', $chars);
    }
}