<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Certificate;
use App\Models\FarmerProfile;
use App\Models\Product;
use App\Models\ProductExportDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExportWebsiteTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_open_export_home_in_english_without_changing_marketplace_language(): void
    {
        $this->get(route('export.home'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee('Browse products');

        $this->withSession(['export_locale' => 'id'])
            ->get(route('export.home'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('<html lang="id">', false);
    }

    public function test_marketplace_only_product_is_absent_from_export_catalog_and_detail_returns_404(): void
    {
        $product = $this->createVisibleProduct([
            'status' => 'approved',
            'show_in_marketplace' => true,
            'show_in_export' => false,
        ]);

        $this->get(route('export.products.index'))
            ->assertOk()
            ->assertDontSee('International Product Name');

        $this->get(route('export.products.show', $product->slug))
            ->assertNotFound();
    }

    public function test_search_and_category_filter_only_show_matching_export_products(): void
    {
        $vegetables = Category::factory()->create(['name' => 'Sayur', 'slug' => 'sayur']);
        $fruits = Category::factory()->create(['name' => 'Buah', 'slug' => 'buah']);
        $specialty = Category::factory()->create(['name' => 'Specialty Produce', 'slug' => 'specialty-produce']);
        $this->createVisibleProduct(['category_id' => $vegetables->id], [
            'name_en' => 'Fresh Green Beans',
        ]);
        $this->createVisibleProduct(['category_id' => $fruits->id], [
            'name_en' => 'Fresh Green Mango',
        ]);
        $this->createVisibleProduct(['category_id' => $specialty->id], [
            'name_en' => 'Specialty Produce Selection',
        ]);

        $this->get(route('export.products.index', ['q' => 'Beans', 'category' => 'sayur']))
            ->assertOk()
            ->assertSee('Fresh Green Beans')
            ->assertSee('Vegetables')
            ->assertDontSee('Fresh Green Mango');

        $this->get(route('export.products.index', ['category' => 'specialty-produce']))
            ->assertOk()
            ->assertSee('Specialty Produce');
    }

    public function test_product_detail_shows_technical_specifications_without_farmer_price_or_stock(): void
    {
        $product = $this->createVisibleProduct([
            'price_idr' => 987654,
            'stock' => 54321,
        ], [
            'name_en' => 'International Product Name',
            'description_en' => 'Export-ready product description.',
            'hs_code' => '1234.56',
            'moisture_percent' => 12.5,
            'size_spec' => 'Medium grade',
            'monthly_capacity' => 2500,
            'monthly_capacity_unit' => 'kg',
        ], 'Private Farmer Name');

        $this->get(route('export.products.show', $product->slug))
            ->assertOk()
            ->assertSee('International Product Name')
            ->assertSee('1234.56')
            ->assertSee('12.50%')
            ->assertSee('Medium grade')
            ->assertSee('2,500 kg / month')
            ->assertDontSee('Private Farmer Name')
            ->assertDontSee('987654')
            ->assertDontSee('54321');
    }

    public function test_export_catalog_paginates_visible_products(): void
    {
        for ($index = 1; $index <= 13; $index++) {
            $this->createVisibleProduct([], ['name_en' => 'Catalog Product '.$index]);
        }

        $this->get(route('export.products.index'))
            ->assertOk()
            ->assertSee('Catalog Product 13')
            ->assertDontSee('Catalog Product 1</h2>', false)
            ->assertSee('Next');

        $this->get(route('export.products.index', ['page' => 2]))
            ->assertOk()
            ->assertSee('Catalog Product 1</h2>', false);
    }

    public function test_export_home_displays_no_more_than_six_latest_products(): void
    {
        for ($index = 1; $index <= 7; $index++) {
            $this->createVisibleProduct([], ['name_en' => 'Featured Product '.$index]);
        }

        $this->get(route('export.home'))
            ->assertOk()
            ->assertSee('Featured Product 7')
            ->assertDontSee('Featured Product 1');
    }

    public function test_product_detail_hides_empty_optional_specification_rows(): void
    {
        $product = $this->createVisibleProduct([], [
            'hs_code' => null,
            'moisture_percent' => null,
            'size_spec' => null,
            'monthly_capacity' => null,
            'monthly_capacity_unit' => null,
        ]);

        $this->get(route('export.products.show', $product->slug))
            ->assertOk()
            ->assertDontSee('HS Code')
            ->assertDontSee('Moisture content')
            ->assertDontSee('Size/specification')
            ->assertDontSee('Monthly supply capacity')
            ->assertSee('Category');
    }

    public function test_certifications_page_and_export_product_detail_only_show_associated_certificates(): void
    {
        $visibleProduct = $this->createVisibleProduct([], ['name_en' => 'Certified Green Beans']);
        $hiddenProduct = $this->createVisibleProduct(['show_in_export' => false], ['name_en' => 'Hidden Mango']);
        $visibleCertificate = Certificate::factory()->create(['name' => 'Organic Certificate']);
        $hiddenCertificate = Certificate::factory()->create(['name' => 'Unlisted Certificate']);
        $visibleProduct->certificates()->attach($visibleCertificate);
        $hiddenProduct->certificates()->attach($hiddenCertificate);

        $this->get(route('export.certifications.index'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee('Organic Certificate')
            ->assertSee('Certified Green Beans')
            ->assertDontSee('Unlisted Certificate')
            ->assertDontSee('Hidden Mango');

        $this->get(route('export.products.show', $visibleProduct->slug))
            ->assertOk()
            ->assertSee('Organic Certificate')
            ->assertSee('View certificate');
    }

    public function test_export_certificates_show_metadata_and_hide_expired_or_unassociated_certificates(): void
    {
        $this->travelTo(now()->startOfDay());
        $visibleProduct = $this->createVisibleProduct([], ['name_en' => 'Compliant Green Beans']);
        $expiredProduct = $this->createVisibleProduct([], ['name_en' => 'Expired Mango']);
        $hiddenProduct = $this->createVisibleProduct(['show_in_export' => false], ['name_en' => 'Private Rice']);
        $validCertificate = Certificate::factory()->create([
            'name' => 'Fair Trade Verified',
            'type' => 'fair_trade',
            'issuer' => 'Global Standards Body',
            'certificate_number' => 'FT-2026-10',
            'expires_at' => now()->addDays(30)->toDateString(),
        ]);
        $expiredCertificate = Certificate::factory()->create([
            'name' => 'Expired Organic Document',
            'expires_at' => now()->subDay()->toDateString(),
        ]);
        Certificate::factory()->create(['name' => 'Unassociated Document']);
        $hiddenProductCertificate = Certificate::factory()->create(['name' => 'Private Product Document']);
        $visibleProduct->certificates()->attach($validCertificate);
        $expiredProduct->certificates()->attach($expiredCertificate);
        $hiddenProduct->certificates()->attach($hiddenProductCertificate);

        $this->get(route('export.certifications.index'))
            ->assertOk()
            ->assertSee('Fair Trade Verified')
            ->assertSee('Fair Trade')
            ->assertSee('Global Standards Body')
            ->assertSee(now()->addDays(30)->format('M j, Y'))
            ->assertSee('Compliant Green Beans')
            ->assertDontSee('Expired Organic Document')
            ->assertDontSee('Unassociated Document')
            ->assertDontSee('Private Product Document');

        $this->get(route('export.products.show', $visibleProduct->slug))
            ->assertOk()
            ->assertSee('Fair Trade Verified')
            ->assertSee('Fair Trade')
            ->assertDontSee('Expired Organic Document');

        $this->get(route('export.products.show', $expiredProduct->slug))
            ->assertOk()
            ->assertDontSee('Expired Organic Document');
    }

    public function test_guest_can_open_empty_export_certifications_page(): void
    {
        $this->get(route('export.certifications.index'))
            ->assertOk()
            ->assertSee('No certificates are available yet.');
    }

    /**
     * @param  array<string, mixed>  $productAttributes
     * @param  array<string, mixed>  $detailsAttributes
     */
    private function createVisibleProduct(
        array $productAttributes = [],
        array $detailsAttributes = [],
        string $farmerName = 'Export Farmer',
    ): Product {
        $farmer = User::factory()->create([
            'name' => $farmerName,
            'role' => 'farmer',
        ]);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);
        $product = Product::factory()->for($farmer, 'farmer')->create(array_merge([
            'status' => 'approved',
            'show_in_export' => true,
        ], $productAttributes));

        ProductExportDetail::factory()->for($product)->create(array_merge([
            'name_en' => 'International Product Name',
            'description_en' => 'Export product description.',
        ], $detailsAttributes));

        return $product;
    }
}