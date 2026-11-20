<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/** Menambah lima kategori utama NUSA RESOURCE. Aman dijalankan berulang; kategori lama tidak disentuh. */
class CategorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            'kopi' => 'Kopi',
            'vanili' => 'Vanili',
            'cengkeh' => 'Cengkeh',
            'beras-merah' => 'Beras Merah',
            'jagung' => 'Jagung',
        ] as $slug => $name) {
            Category::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
