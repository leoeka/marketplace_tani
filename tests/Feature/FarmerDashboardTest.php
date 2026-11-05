<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class FarmerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_create_update_and_delete_their_own_product(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();
        $this->actingAs($farmer);

        $this->post(route('farmer.products.store'), [
            'name' => 'Tomat Merah',
            'category_id' => $category->id,
            'description' => 'Tomat segar dari kebun.',
            'unit' => 'kg',
            'price_idr' => 12000,
            'stock' => 40,
            'action' => 'draft',
            'photos' => [UploadedFile::fake()->image('tomat.png')],
        ])->assertRedirect(route('farmer.products.index'));

        $product = Product::query()->where('name', 'Tomat Merah')->firstOrFail();
        $this->assertSame('draft', $product->status);
        $this->assertSame($farmer->id, $product->farmer_id);
        $image = ProductImage::query()->where('product_id', $product->id)->firstOrFail();
        Storage::disk('public')->assertExists($image->path);

        $this->put(route('farmer.products.update', $product), [
            'name' => 'Tomat Merah Pilihan',
            'category_id' => $category->id,
            'description' => 'Tomat pilihan dari kebun.',
            'unit' => 'kg',
            'price_idr' => 15000,
            'stock' => 35,
            'action' => 'draft',
        ])->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Tomat Merah Pilihan',
            'price_idr' => 15000,
            'status' => 'draft',
        ]);

        $this->delete(route('farmer.products.destroy', $product))
            ->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing($image->path);
    }

    public function test_farmer_cannot_update_another_farmers_product(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        $otherFarmer = User::factory()->create(['role' => 'farmer']);
        $product = Product::factory()->for($otherFarmer, 'farmer')->create(['name' => 'Produk Petani B']);

        $this->actingAs($farmer)
            ->put(route('farmer.products.update', $product), [])
            ->assertForbidden();

        $this->assertFalse(Gate::forUser($farmer)->allows('update', $product));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Produk Petani B',
        ]);
    }

    public function test_unverified_farmer_cannot_submit_a_product_for_approval(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->post(route('farmer.products.store'), [
                'name' => 'Bayam Segar',
                'category_id' => $category->id,
                'description' => 'Bayam dari kebun.',
                'unit' => 'kg',
                'price_idr' => 7000,
                'stock' => 20,
                'action' => 'submit',
                'photos' => [UploadedFile::fake()->image('bayam.jpg')],
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('products', ['name' => 'Bayam Segar']);
        Storage::disk('public')->assertEmpty();
    }

    public function test_verified_farmer_can_submit_a_product_for_approval(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->post(route('farmer.products.store'), [
                'name' => 'Cabai Merah',
                'category_id' => $category->id,
                'description' => 'Cabai segar dari petani.',
                'unit' => 'kg',
                'price_idr' => 25000,
                'stock' => 12,
                'action' => 'submit',
                'photos' => [UploadedFile::fake()->image('cabai.jpg')],
            ])
            ->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseHas('products', [
            'farmer_id' => $farmer->id,
            'name' => 'Cabai Merah',
            'status' => 'pending',
        ]);
    }

    public function test_farmer_can_save_a_preorder_with_a_valid_harvest_date(): void
    {
        Storage::fake('public');
        $harvestDate = now()->toDateString();
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->post(route('farmer.products.store'), [
                ...$this->productData($category),
                'is_preorder' => '1',
                'harvest_date' => $harvestDate,
                'photos' => [UploadedFile::fake()->image('panen.jpg')],
            ])
            ->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseHas('products', [
            'farmer_id' => $farmer->id,
            'name' => 'Hasil Panen Terjadwal',
        ]);
        $product = Product::query()->where('name', 'Hasil Panen Terjadwal')->firstOrFail();
        /** @var \Illuminate\Support\Carbon $productHarvestDate */
        $productHarvestDate = $product->harvest_date;
        $this->assertSame($harvestDate, $productHarvestDate->toDateString());
    }

    public function test_preorder_requires_a_harvest_date(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->from(route('farmer.products.create'))
            ->post(route('farmer.products.store'), [
                ...$this->productData($category),
                'is_preorder' => '1',
                'photos' => [UploadedFile::fake()->image('panen.jpg')],
            ])
            ->assertSessionHasErrors('harvest_date');

        $this->assertDatabaseMissing('products', ['name' => 'Hasil Panen Terjadwal']);
    }

    public function test_preorder_rejects_a_harvest_date_before_today(): void
    {
        Storage::fake('public');
        $pastDate = now()->subDay()->toDateString();
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->from(route('farmer.products.create'))
            ->post(route('farmer.products.store'), [
                ...$this->productData($category),
                'is_preorder' => '1',
                'harvest_date' => $pastDate,
                'photos' => [UploadedFile::fake()->image('panen.jpg')],
            ])
            ->assertSessionHasErrors('harvest_date');

        $this->assertDatabaseMissing('products', ['name' => 'Hasil Panen Terjadwal']);
    }

    public function test_harvest_date_is_cleared_when_preorder_is_not_selected(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->post(route('farmer.products.store'), [
                ...$this->productData($category),
                'harvest_date' => now()->addDays(15)->toDateString(),
                'photos' => [UploadedFile::fake()->image('produk.jpg')],
            ])
            ->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseHas('products', [
            'name' => 'Hasil Panen Terjadwal',
            'harvest_date' => null,
        ]);
    }

    public function test_farmer_can_save_a_shop_profile_with_a_generated_slug(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->put(route('farmer.profile.update'), [
                'shop_name' => 'Kebun Subur',
                'group_name' => 'Poktan Sejahtera',
                'region' => 'Garut, Jawa Barat',
                'story' => 'Kami menanam hasil bumi dengan sepenuh hati.',
                'photo' => UploadedFile::fake()->image('toko.png'),
            ])
            ->assertRedirect(route('farmer.profile.edit'));

        $this->assertDatabaseHas('farmer_profiles', [
            'user_id' => $farmer->id,
            'shop_name' => 'Kebun Subur',
            'slug' => 'kebun-subur',
        ]);

        $profile = $farmer->fresh()->farmerProfile;
        Storage::disk('public')->assertExists($profile->photo);
    }

    public function test_customer_cannot_open_farmer_dashboard(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)
            ->get(route('farmer.dashboard'))
            ->assertForbidden();
    }

    public function test_editing_an_approved_product_returns_it_to_pending(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);
        $category = Category::factory()->create();
        $product = Product::factory()->for($farmer, 'farmer')->for($category)->create([
            'status' => 'approved',
        ]);
        ProductImage::factory()->for($product)->create();

        $this->actingAs($farmer)
            ->put(route('farmer.products.update', $product), [
                'name' => $product->name,
                'category_id' => $category->id,
                'description' => $product->description,
                'unit' => $product->unit,
                'price_idr' => $product->price_idr,
                'stock' => $product->stock,
                'action' => 'draft',
            ])
            ->assertRedirect(route('farmer.products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'status' => 'pending',
        ]);
    }

    public function test_product_upload_rejects_non_image_files(): void
    {
        Storage::fake('public');
        $farmer = User::factory()->create(['role' => 'farmer']);
        $category = Category::factory()->create();

        $this->actingAs($farmer)
            ->from(route('farmer.products.create'))
            ->post(route('farmer.products.store'), [
                'name' => 'Produk Tidak Valid',
                'category_id' => $category->id,
                'description' => 'Deskripsi produk.',
                'unit' => 'kg',
                'price_idr' => 5000,
                'stock' => 10,
                'action' => 'draft',
                'photos' => [UploadedFile::fake()->create('catatan.txt', 10, 'text/plain')],
            ])
            ->assertSessionHasErrors('photos.0');

        $this->assertDatabaseMissing('products', ['name' => 'Produk Tidak Valid']);
        Storage::disk('public')->assertEmpty();
    }

    /** @return array<string, mixed> */
    private function productData(Category $category): array
    {
        return [
            'name' => 'Hasil Panen Terjadwal',
            'category_id' => $category->id,
            'description' => 'Hasil bumi siap dipesan.',
            'unit' => 'kg',
            'price_idr' => 10000,
            'stock' => 25,
            'action' => 'draft',
        ];
    }
}