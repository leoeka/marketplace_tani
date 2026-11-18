<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardLanguageTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_is_indonesian_by_default(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('lang="id"', false)
            ->assertSee('Ringkasan')
            ->assertDontSee('Overview');
    }

    public function test_admin_can_switch_dashboard_to_english_and_back(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->from(route('admin.dashboard'))
            ->post(route('dashboard.locale', 'en'))
            ->assertRedirect(route('admin.dashboard'));

        $this->assertSame('en', $admin->fresh()->locale);

        $this->actingAs($admin->fresh())->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Overview')
            ->assertSee('Farmers')
            ->assertDontSee('Ringkasan');

        $this->actingAs($admin->fresh())->post(route('dashboard.locale', 'id'));

        $this->actingAs($admin->fresh())->get(route('admin.dashboard'))
            ->assertSee('Ringkasan')
            ->assertDontSee('Overview');
    }

    public function test_farmer_dashboard_follows_the_account_language(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer', 'locale' => 'en']);

        $this->actingAs($farmer)->get(route('farmer.dashboard'))
            ->assertOk()
            ->assertSee('lang="en"', false)
            ->assertSee('Overview')
            ->assertSee('My Products')
            ->assertDontSee('Produk Saya');
    }

    public function test_farmer_pages_render_in_english(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer', 'locale' => 'en']);

        foreach (['farmer.products.index', 'farmer.orders.index', 'farmer.profile.edit'] as $route) {
            $this->actingAs($farmer)->get(route($route))
                ->assertOk()
                ->assertSee('lang="en"', false)
                ->assertDontSee('Pesanan Masuk');
        }
    }

    public function test_admin_pages_render_in_english(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'locale' => 'en']);

        foreach (['admin.farmers.index', 'admin.products.index', 'admin.orders.index', 'admin.rfqs.index', 'admin.certificates.index', 'admin.market-prices.index', 'admin.articles.index', 'admin.shipping-services.index', 'admin.loading-ports.index', 'admin.freight-estimates.index', 'admin.locations.index'] as $route) {
            $this->actingAs($admin)->get(route($route))
                ->assertOk()
                ->assertSee('lang="en"', false);
        }
    }

    public function test_account_with_unknown_locale_falls_back_to_indonesian(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'locale' => 'zz']);

        $this->actingAs($admin)->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Ringkasan');
    }

    public function test_unsupported_locale_is_not_found(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('dashboard.locale', 'fr'))->assertNotFound();
        $this->assertNull($admin->fresh()->locale);
    }

    public function test_customers_cannot_change_the_dashboard_language(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($customer)->post(route('dashboard.locale', 'en'))->assertForbidden();
        $this->assertNull($customer->fresh()->locale);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->post(route('dashboard.locale', 'en'))->assertRedirect(route('login'));
    }

    public function test_dashboard_language_does_not_change_the_public_marketplace(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'locale' => 'en']);

        $this->actingAs($admin)->withSession(['locale' => 'id'])->get(route('home'))->assertOk();

        $this->assertSame('id', app()->getLocale());
    }
}