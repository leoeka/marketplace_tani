<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreorderCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_preorder_page_shows_only_current_approved_marketplace_products_from_verified_farmers(): void
    {
        $upcomingHarvestDate = now()->addDays(1)->toDateString();
        $expiredHarvestDate = now()->subDay()->toDateString();
        $verifiedFarmer = $this->createFarmer(true);
        $unverifiedFarmer = $this->createFarmer(false);
        $visible = $this->createProduct($verifiedFarmer, [
            'name' => 'Wortel Panen Besok',
            'harvest_date' => $upcomingHarvestDate,
        ]);
        $this->createProduct($verifiedFarmer, [
            'name' => 'Produk Menunggu',
            'status' => 'pending',
            'harvest_date' => $upcomingHarvestDate,
        ]);
        $this->createProduct($verifiedFarmer, [
            'name' => 'Produk Panen Lampau',
            'harvest_date' => $expiredHarvestDate,
        ]);
        $this->createProduct($unverifiedFarmer, [
            'name' => 'Toko Belum Terverifikasi',
            'harvest_date' => $upcomingHarvestDate,
        ]);
        $this->createProduct($verifiedFarmer, [
            'name' => 'Produk Kanal Lain',
            'show_in_marketplace' => false,
            'harvest_date' => $upcomingHarvestDate,
        ]);

        $this->get(route('marketplace.preorders.index'))
            ->assertOk()
            ->assertSee($visible->name)
            ->assertDontSee('Produk Menunggu')
            ->assertDontSee('Produk Panen Lampau')
            ->assertDontSee('Toko Belum Terverifikasi')
            ->assertDontSee('Produk Kanal Lain');
    }

    public function test_expired_preorder_is_absent_from_preorder_page_but_remains_in_product_catalog(): void
    {
        $farmer = $this->createFarmer(true);
        $expired = $this->createProduct($farmer, [
            'name' => 'Mangga Panen Selesai',
            'harvest_date' => now()->subDay()->toDateString(),
        ]);

        $this->get(route('marketplace.preorders.index'))
            ->assertOk()
            ->assertDontSee($expired->name);

        $this->get(route('marketplace.products.index'))
            ->assertOk()
            ->assertSee($expired->name);
    }

    public function test_preorder_filters_apply_name_and_category(): void
    {
        $farmer = $this->createFarmer(true);
        $vegetables = Category::factory()->create(['name' => 'Sayur', 'slug' => 'sayur']);
        $fruits = Category::factory()->create(['name' => 'Buah', 'slug' => 'buah']);
        $selected = $this->createProduct($farmer, [
            'category_id' => $vegetables->id,
            'name' => 'Wortel Manis',
            'harvest_date' => now()->addDays(2)->toDateString(),
        ]);
        $this->createProduct($farmer, [
            'category_id' => $fruits->id,
            'name' => 'Mangga Manis',
            'harvest_date' => now()->addDays(3)->toDateString(),
        ]);

        $this->get(route('marketplace.preorders.index', ['q' => 'Wortel', 'category' => 'sayur']))
            ->assertOk()
            ->assertSee($selected->name)
            ->assertDontSee('Mangga Manis');
    }

    private function createFarmer(bool $verified): User
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create([
            'verified_at' => $verified ? now() : null,
        ]);

        return $farmer;
    }

    /** @param array<string, mixed> $attributes */
    private function createProduct(User $farmer, array $attributes = []): Product
    {
        $attributes += [
            'status' => 'approved',
            'show_in_marketplace' => true,
            'harvest_date' => now()->addDay()->toDateString(),
        ];

        return Product::factory()
            ->for($farmer, 'farmer')
            ->for(Category::factory())
            ->create($attributes);
    }
}
