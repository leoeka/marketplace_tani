<?php

namespace Tests\Feature;

use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductExportDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductExportVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_scope_only_returns_approved_products_with_channel_verified_farmer_and_complete_details(): void
    {
        $visibleProduct = $this->createProductWithExportDetails();
        $unapprovedProduct = $this->createProductWithExportDetails(['status' => 'pending']);
        $channelDisabledProduct = $this->createProductWithExportDetails(['show_in_export' => false]);
        $unverifiedProduct = $this->createProductWithExportDetails([], false);
        $incompleteNameProduct = $this->createProductWithExportDetails([], true, ['name_en' => '']);
        $incompleteDescriptionProduct = $this->createProductWithExportDetails([], true, ['description_en' => '']);

        $exportProductIds = Product::exportVisible()->pluck('id')->all();

        $this->assertSame([$visibleProduct->id], $exportProductIds);
        $this->assertNotContains($unapprovedProduct->id, $exportProductIds);
        $this->assertNotContains($channelDisabledProduct->id, $exportProductIds);
        $this->assertNotContains($unverifiedProduct->id, $exportProductIds);
        $this->assertNotContains($incompleteNameProduct->id, $exportProductIds);
        $this->assertNotContains($incompleteDescriptionProduct->id, $exportProductIds);
    }

    /**
     * @param  array<string, mixed>  $productAttributes
     * @param  array<string, mixed>  $detailsAttributes
     */
    private function createProductWithExportDetails(
        array $productAttributes = [],
        bool $verified = true,
        array $detailsAttributes = [],
    ): Product {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create([
            'verified_at' => $verified ? now() : null,
        ]);
        $product = Product::factory()->for($farmer, 'farmer')->create(array_merge([
            'status' => 'approved',
            'show_in_export' => true,
        ], $productAttributes));

        ProductExportDetail::factory()->for($product)->create(array_merge([
            'name_en' => 'Export Product',
            'description_en' => 'A complete English description.',
        ], $detailsAttributes));

        return $product;
    }
}