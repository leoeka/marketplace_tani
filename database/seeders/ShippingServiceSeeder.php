<?php

namespace Database\Seeders;

use App\Models\ShippingService;
use Illuminate\Database\Seeder;

class ShippingServiceSeeder extends Seeder
{
    public function run(): void
    {
        ShippingService::query()->firstOrCreate(
            ['name' => 'Kurir Lokal', 'type' => 'local_courier'],
        );
        ShippingService::query()->firstOrCreate(
            ['name' => 'Cold Chain', 'type' => 'cold_chain'],
        );
    }
}
