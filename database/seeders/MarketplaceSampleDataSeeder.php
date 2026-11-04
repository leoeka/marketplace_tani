<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MarketplaceSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Sayur', 'slug' => 'sayur'],
            ['name' => 'Buah', 'slug' => 'buah'],
            ['name' => 'Rempah', 'slug' => 'rempah'],
            ['name' => 'Beras', 'slug' => 'beras'],
        ])->mapWithKeys(fn (array $category): array => [
            $category['slug'] => Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']],
            ),
        ]);

        $farmers = [
            [
                'name' => 'Sari Wulandari',
                'email' => 'demo.farmer.sari@example.test',
                'shop' => 'Kebun Lestari',
                'slug' => 'kebun-lestari-demo',
                'region' => 'Lembang, Jawa Barat',
                'category' => 'sayur',
                'product' => 'Tomat Merah Segar',
                'price' => 12000,
            ],
            [
                'name' => 'Dedi Santoso',
                'email' => 'demo.farmer.dedi@example.test',
                'shop' => 'Tani Makmur',
                'slug' => 'tani-makmur-demo',
                'region' => 'Kopeng, Jawa Tengah',
                'category' => 'buah',
                'product' => 'Jeruk Manis Lokal',
                'price' => 18000,
            ],
            [
                'name' => 'Rina Handayani',
                'email' => 'demo.farmer.rina@example.test',
                'shop' => 'Buah Bumi',
                'slug' => 'buah-bumi-demo',
                'region' => 'Batu, Jawa Timur',
                'category' => 'beras',
                'product' => 'Beras Panen Baru',
                'price' => 16000,
            ],
        ];

        foreach ($farmers as $index => $sample) {
            if (User::query()->where('email', $sample['email'])->exists()) {
                continue;
            }

            $farmer = User::create([
                'name' => $sample['name'],
                'email' => $sample['email'],
                'password' => Str::random(40),
                'role' => 'farmer',
                'customer_type' => 'individual',
            ]);

            FarmerProfile::firstOrCreate(
                ['user_id' => $farmer->id],
                [
                    'shop_name' => $sample['shop'],
                    'slug' => $sample['slug'],
                    'region' => $sample['region'],
                    'story' => 'Hasil bumi pilihan yang dirawat dan dipanen oleh petani lokal.',
                    'verified_at' => now(),
                ],
            );

            Product::firstOrCreate(
                ['slug' => Str::slug($sample['product']).'-demo'],
                [
                    'farmer_id' => $farmer->id,
                    'category_id' => $categories[$sample['category']]->id,
                    'name' => $sample['product'],
                    'description' => 'Hasil panen berkualitas langsung dari kebun petani.',
                    'unit' => 'kg',
                    'price_idr' => $sample['price'],
                    'stock' => 100 + ($index * 25),
                    'status' => 'approved',
                    'show_in_marketplace' => true,
                ],
            );
        }
    }
}