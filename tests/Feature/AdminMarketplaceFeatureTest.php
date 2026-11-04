<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMarketplaceFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_has_no_legacy_placeholder_or_duplicate_navigation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertDontSee('Selamat datang di panel master admin');

        $this->assertSame(1, substr_count($response->getContent(), '<nav'));
    }

    public function test_admin_can_verify_and_revoke_farmer_verification(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = User::factory()->create(['role' => 'farmer']);
        $profile = FarmerProfile::factory()->for($farmer)->create(['verified_at' => null]);

        $this->actingAs($admin)
            ->post(route('admin.farmers.verify', $profile))
            ->assertRedirect();

        $this->assertNotNull($profile->fresh()->verified_at);

        $this->actingAs($admin)
            ->delete(route('admin.farmers.revoke', $profile))
            ->assertRedirect();

        $this->assertNull($profile->fresh()->verified_at);
    }

    public function test_admin_can_approve_product_and_choose_publication_channels(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = $this->createFarmer(true);
        $product = Product::factory()->for($farmer, 'farmer')->create([
            'status' => 'pending',
            'rejection_note' => 'Catatan lama',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.approve', $product), [
                'show_in_marketplace' => '1',
                'show_in_export' => '1',
            ])
            ->assertRedirect(route('admin.products.show', $product))
            ->assertSessionHas('success', 'Produk berhasil disetujui.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'approved',
            'rejection_note' => null,
            'show_in_marketplace' => true,
            'show_in_export' => true,
        ]);
    }

    public function test_admin_product_details_check_saved_publication_channels(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = $this->createFarmer(true);
        $product = Product::factory()->for($farmer, 'farmer')->create([
            'status' => 'approved',
            'show_in_marketplace' => true,
            'show_in_export' => false,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertSee('name="show_in_marketplace" value="1" checked', false)
            ->assertDontSee('name="show_in_export" value="1" checked', false)
            ->assertSee('Simpan kanal');
    }

    public function test_admin_can_save_channels_without_changing_approved_product_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = $this->createFarmer(true);
        $product = Product::factory()->for($farmer, 'farmer')->create([
            'status' => 'approved',
            'show_in_marketplace' => true,
            'show_in_export' => false,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.channels.update', $product), [
                'show_in_marketplace' => '1',
                'show_in_export' => '1',
            ])
            ->assertRedirect(route('admin.products.show', $product))
            ->assertSessionHas('success', 'Kanal publikasi produk berhasil disimpan.');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'approved',
            'show_in_marketplace' => true,
            'show_in_export' => true,
        ]);
    }

    public function test_admin_must_provide_a_reason_to_reject_a_product(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = $this->createFarmer(true);
        $product = Product::factory()->for($farmer, 'farmer')->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->from(route('admin.products.show', $product))
            ->post(route('admin.products.reject', $product), ['rejection_note' => ''])
            ->assertSessionHasErrors('rejection_note');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'pending',
            'rejection_note' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.products.reject', $product), ['rejection_note' => 'Foto produk kurang jelas.'])
            ->assertRedirect(route('admin.products.show', $product));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'rejected',
            'rejection_note' => 'Foto produk kurang jelas.',
            'show_in_marketplace' => false,
            'show_in_export' => false,
        ]);
    }

    public function test_approval_requires_at_least_one_publication_channel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $farmer = $this->createFarmer(true);
        $product = Product::factory()->for($farmer, 'farmer')->create(['status' => 'pending']);

        $this->actingAs($admin)
            ->from(route('admin.products.show', $product))
            ->post(route('admin.products.approve', $product), [])
            ->assertSessionHasErrors('channels');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'pending',
            'show_in_marketplace' => false,
            'show_in_export' => false,
        ]);
    }

    public function test_farmer_and_customer_cannot_open_admin_panel(): void
    {
        foreach (['farmer', 'customer'] as $role) {
            $user = User::factory()->create(['role' => $role]);

            $this->actingAs($user)
                ->get(route('admin.dashboard'))
                ->assertForbidden();
        }
    }

    public function test_non_marketplace_products_are_hidden_and_product_detail_returns_404(): void
    {
        $farmer = $this->createFarmer(true);
        $category = Category::factory()->create();
        $hiddenProducts = collect(['draft', 'pending', 'rejected'])->map(fn (string $status): Product => Product::factory()
            ->for($farmer, 'farmer')
            ->for($category)
            ->create([
                'name' => 'Produk '.$status,
                'status' => $status,
                'show_in_marketplace' => true,
            ]));
        $hiddenProducts->push(Product::factory()->for($farmer, 'farmer')->for($category)->create([
            'name' => 'Produk Kanal Mati',
            'status' => 'approved',
            'show_in_marketplace' => false,
        ]));

        $response = $this->get(route('marketplace.products.index'))
            ->assertOk();

        foreach ($hiddenProducts as $product) {
            $response->assertDontSee($product->name);
            $this->get(route('marketplace.products.show', $product->slug))->assertNotFound();
        }
    }

    public function test_homepage_only_lists_approved_products_and_verified_farmers(): void
    {
        $verifiedFarmer = $this->createFarmer(true);
        $unverifiedFarmer = $this->createFarmer(false);
        $visibleProduct = Product::factory()->for($verifiedFarmer, 'farmer')->create([
            'name' => 'Produk Beranda Tampil',
            'status' => 'approved',
            'show_in_marketplace' => true,
        ]);
        Product::factory()->for($verifiedFarmer, 'farmer')->create([
            'name' => 'Produk Beranda Tersembunyi',
            'status' => 'pending',
            'show_in_marketplace' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($visibleProduct->name)
            ->assertDontSee('Produk Beranda Tersembunyi')
            ->assertSee($verifiedFarmer->farmerProfile->shop_name)
            ->assertDontSee($unverifiedFarmer->farmerProfile->shop_name);
    }

    public function test_product_catalog_filters_by_category_and_name_search(): void
    {
        $farmer = $this->createFarmer(true);
        $vegetables = Category::factory()->create(['name' => 'Sayur', 'slug' => 'sayur']);
        $fruits = Category::factory()->create(['name' => 'Buah', 'slug' => 'buah']);
        $tomato = Product::factory()->for($farmer, 'farmer')->for($vegetables)->create([
            'name' => 'Tomat Merah',
            'status' => 'approved',
            'show_in_marketplace' => true,
        ]);
        $spinach = Product::factory()->for($farmer, 'farmer')->for($vegetables)->create([
            'name' => 'Bayam Hijau',
            'status' => 'approved',
            'show_in_marketplace' => true,
        ]);
        $tomatoFruit = Product::factory()->for($farmer, 'farmer')->for($fruits)->create([
            'name' => 'Tomat Ceri',
            'status' => 'approved',
            'show_in_marketplace' => true,
        ]);
        $orange = Product::factory()->for($farmer, 'farmer')->for($fruits)->create([
            'name' => 'Jeruk Manis',
            'status' => 'approved',
            'show_in_marketplace' => true,
        ]);

        $this->get(route('marketplace.products.index', ['category' => 'sayur', 'q' => 'tomat']))
            ->assertOk()
            ->assertSee($tomato->name)
            ->assertDontSee($spinach->name)
            ->assertDontSee($orange->name);

        $this->get(route('marketplace.products.index', ['category' => 'sayur']))
            ->assertOk()
            ->assertSee($tomato->name)
            ->assertSee($spinach->name)
            ->assertDontSee($tomatoFruit->name);

        $this->get(route('marketplace.products.index', ['q' => 'tomat']))
            ->assertOk()
            ->assertSee($tomato->name)
            ->assertSee($tomatoFruit->name)
            ->assertDontSee($spinach->name);
    }

    public function test_farmer_profile_only_shows_approved_marketplace_products(): void
    {
        $farmer = $this->createFarmer(true);
        $category = Category::factory()->create();
        $visibleProduct = Product::factory()->for($farmer, 'farmer')->for($category)->create([
            'name' => 'Produk Tampil',
            'status' => 'approved',
            'show_in_marketplace' => true,
        ]);
        $pendingProduct = Product::factory()->for($farmer, 'farmer')->for($category)->create([
            'name' => 'Produk Menunggu',
            'status' => 'pending',
            'show_in_marketplace' => true,
        ]);

        $this->get(route('marketplace.farmers.show', $farmer->farmerProfile->slug))
            ->assertOk()
            ->assertSee($visibleProduct->name)
            ->assertDontSee($pendingProduct->name);
    }

    public function test_unverified_farmer_profile_is_not_public(): void
    {
        $farmer = $this->createFarmer(false);

        $this->get(route('marketplace.farmers.show', $farmer->farmerProfile->slug))
            ->assertNotFound();
    }

    public function test_farmer_can_read_rejection_reason_edit_product_and_resubmit(): void
    {
        $farmer = $this->createFarmer(true);
        $category = Category::factory()->create();
        $product = Product::factory()->for($farmer, 'farmer')->for($category)->create([
            'name' => 'Produk Ditolak',
            'status' => 'rejected',
            'rejection_note' => 'Mohon unggah foto yang lebih jelas.',
        ]);
        ProductImage::factory()->for($product)->create();

        $this->actingAs($farmer)
            ->get(route('farmer.dashboard'))
            ->assertOk()
            ->assertSee('Mohon unggah foto yang lebih jelas.');

        $this->actingAs($farmer)
            ->put(route('farmer.products.update', $product), [
                'name' => 'Produk Ditolak Diperbaiki',
                'category_id' => $category->id,
                'description' => $product->description,
                'unit' => $product->unit,
                'price_idr' => $product->price_idr,
                'stock' => $product->stock,
                'action' => 'submit',
            ])
            ->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Ditolak Diperbaiki',
            'status' => 'pending',
            'rejection_note' => null,
        ]);
    }

    private function createFarmer(bool $verified): User
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create([
            'verified_at' => $verified ? now() : null,
        ]);

        return $farmer;
    }
}