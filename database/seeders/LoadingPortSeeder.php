<?php

namespace Database\Seeders;

use App\Models\LoadingPort;
use Illuminate\Database\Seeder;

class LoadingPortSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['name' => 'Tanjung Priok', 'city' => 'Jakarta'],
            ['name' => 'Tanjung Perak', 'city' => 'Surabaya'],
            ['name' => 'Benoa', 'city' => 'Bali'],
        ] as $port) {
            LoadingPort::query()->updateOrCreate(
                ['name' => $port['name']],
                ['city' => $port['city']],
            );
        }
    }
}