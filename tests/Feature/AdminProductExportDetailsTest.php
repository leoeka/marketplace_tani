<?php

namespace Tests\Feature;

use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductExportDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminProductExportDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_product_export_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct();
        $exportDetails = [
            'name_en' => 'Premium Green Beans',
            'description_en' => 'Freshly harvested green beans.',
            'hs_code' => '0708.20',
            'moisture_percent' => '11.50',
            'size_spec' => 'Uniform medium size',
            'monthly_capacity' => '12000',
            'monthly_capacity_unit' => 'kg',
        ];

        $this->actingAs($admin)
            ->put(route('admin.products.export-details.update', $product), $exportDetails)
            ->assertRedirect(route('admin.products.show', $product))
            ->assertSessionHas('success', 'Data ekspor berhasil disimpan.');

        $this->assertDatabaseHas('product_export_details', [
            'product_id' => $product->id,
            'name_en' => 'Premium Green Beans',
            'hs_code' => '0708.20',
            'monthly_capacity' => 12000,
            'monthly_capacity_unit' => 'kg',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.products.export-details.update', $product), array_merge($exportDetails, [
                'name_en' => 'Updated Green Beans',
                'monthly_capacity' => '15000',
                'monthly_capacity_unit' => 'ton',
            ]))
            ->assertRedirect(route('admin.products.show', $product));

        $this->assertDatabaseCount('product_export_details', 1);
        $this->assertDatabaseHas('product_export_details', [
            'product_id' => $product->id,
            'name_en' => 'Updated Green Beans',
            'monthly_capacity' => 15000,
            'monthly_capacity_unit' => 'ton',
        ]);
    }

    public function test_admin_sees_export_visibility_reasons_on_product_details(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct(['status' => 'pending', 'show_in_export' => false]);
        ProductExportDetail::factory()->for($product)->create(['name_en' => '']);
        $product->farmer->farmerProfile()->update(['verified_at' => null]);

        $this->actingAs($admin)
            ->get(route('admin.products.show', $product))
            ->assertOk()
            ->assertSee('Belum tampil')
            ->assertSee('Produk belum disetujui.')
            ->assertSee('Kanal ekspor belum dicentang.')
            ->assertSee('Data ekspor belum lengkap.')
            ->assertSee('Petani belum terverifikasi.');
    }

    public function test_admin_rejects_an_invalid_hs_code(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct();

        $this->actingAs($admin)
            ->from(route('admin.products.show', $product))
            ->put(route('admin.products.export-details.update', $product), $this->validExportDetails(['hs_code' => '12-34']))
            ->assertSessionHasErrors('hs_code');

        $this->assertDatabaseMissing('product_export_details', ['product_id' => $product->id]);
    }

    public function test_admin_rejects_moisture_outside_zero_to_one_hundred(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct();

        $this->actingAs($admin)
            ->from(route('admin.products.show', $product))
            ->put(route('admin.products.export-details.update', $product), $this->validExportDetails(['moisture_percent' => '100.01']))
            ->assertSessionHasErrors('moisture_percent');

        $this->assertDatabaseMissing('product_export_details', ['product_id' => $product->id]);
    }

    public function test_admin_must_provide_a_unit_with_monthly_capacity(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createProduct();

        $this->actingAs($admin)
            ->from(route('admin.products.show', $product))
            ->put(route('admin.products.export-details.update', $product), $this->validExportDetails([
                'monthly_capacity' => '500',
                'monthly_capacity_unit' => '',
            ]))
            ->assertSessionHasErrors('monthly_capacity_unit');

        $this->assertDatabaseMissing('product_export_details', ['product_id' => $product->id]);
    }

    /** @return array<string, array<int, string>> */
    public static function nonAdminRoles(): array
    {
        return [
            'farmer' => ['farmer'],
            'customer' => ['customer'],
        ];
    }

    #[DataProvider('nonAdminRoles')]
    public function test_non_admin_roles_receive_forbidden_for_export_form(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $product = $this->createProduct();

        $this->actingAs($user)
            ->put(route('admin.products.export-details.update', $product), $this->validExportDetails())
            ->assertForbidden();
    }

    /** @param array<string, mixed> $attributes */
    private function createProduct(array $attributes = []): Product
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);

        return Product::factory()->for($farmer, 'farmer')->create($attributes);
    }

    /** @param array<string, mixed> $overrides */
    private function validExportDetails(array $overrides = []): array
    {
        return array_merge([
            'name_en' => 'Export Product',
            'description_en' => 'A valid export product description.',
            'hs_code' => '',
            'moisture_percent' => '',
            'size_spec' => '',
            'monthly_capacity' => '',
            'monthly_capacity_unit' => '',
        ], $overrides);
    }
}