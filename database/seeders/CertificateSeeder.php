<?php

namespace Database\Seeders;

use App\Models\Certificate;
use Illuminate\Database\Seeder;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Organic', 'Halal', 'Fair Trade', 'HACCP', 'ISO', 'BPOM', 'Phytosanitary Certificate'] as $name) {
            Certificate::query()->firstOrCreate(
                ['name' => $name],
                ['file_path' => null],
            );
        }
    }
}