<?php

namespace App\Console\Commands;

use App\Support\MachineTranslator;
use Illuminate\Console\Command;

class ExportTranslate extends Command
{
    protected $signature = 'export:translate
        {locales?* : Kode bahasa, kosong = semua bahasa otomatis yang aktif}
        {--site=all : export, marketplace, atau all}';

    protected $description = 'Menerjemahkan teks website ekspor dan/atau marketplace ke bahasa otomatis dan menyimpannya (pemanasan cache)';

    public function handle(MachineTranslator $translator): int
    {
        if (! $translator->enabled()) {
            $this->error('Terjemahan otomatis mati: GOOGLE_TRANSLATE_API_KEY belum diisi atau EXPORT_MT_ENABLED=false.');

            return self::FAILURE;
        }

        $site = (string) $this->option('site');
        $groups = match ($site) {
            'export' => ['export'],
            'marketplace' => MachineTranslator::MARKETPLACE_GROUPS,
            default => ['export', ...MachineTranslator::MARKETPLACE_GROUPS],
        };

        $locales = $this->argument('locales') ?: (array) config('export.machine_locales', []);

        foreach ($locales as $locale) {
            $before = $translator->usedThisMonth();
            $done = 0;

            foreach ($groups as $group) {
                if (! MachineTranslator::groupNeedsMachine($locale, $group)) {
                    continue;
                }

                $translator->buildTree($locale, $group);
                $done++;
            }

            if ($done === 0) {
                $this->warn("{$locale}: dilewati (bukan bahasa otomatis, atau sudah punya file lang sendiri).");

                continue;
            }

            $this->line(sprintf('%s: selesai (%d grup), +%d karakter terpakai.', $locale, $done, $translator->usedThisMonth() - $before));
        }

        $this->info(sprintf(
            'Pemakaian bulan ini: %d dari batas %d karakter.',
            $translator->usedThisMonth(),
            (int) config('export.machine.monthly_char_limit')
        ));

        return self::SUCCESS;
    }
}