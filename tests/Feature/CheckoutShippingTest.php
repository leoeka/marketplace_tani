<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\FarmerProfile;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutShippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_requires_a_shipping_service_when_one_is_active(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        ShippingService::factory()->create(['is_active' => true]);
        $product = $this->createMarketplaceProduct();
        $this->actingAs($customer)->post(route('cart.items.store', $product->id));

        $this->from(route('marketplace.checkout.index'))
            ->post(route('marketplace.checkout.store'), $this->checkoutData())
            ->assertSessionHasErrors('shipping_service_id');

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_checkout_rejects_nonexistent_and_inactive_shipping_services(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $inactiveService = ShippingService::factory()->create(['is_active' => false]);
        $product = $this->createMarketplaceProduct();
        $this->actingAs($customer)->post(route('cart.items.store', $product->id));

        foreach ([999999, $inactiveService->id] as $shippingServiceId) {
            $this->from(route('marketplace.checkout.index'))
                ->post(route('marketplace.checkout.store'), $this->checkoutData($shippingServiceId))
                ->assertSessionHasErrors('shipping_service_id');
        }

        $this->assertDatabaseCount('orders', 0);
        $this->assertSame(10, $product->fresh()->stock);
    }

    public function test_checkout_shows_active_services_and_saves_service_snapshot_on_order(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $shippingService = ShippingService::factory()->create([
            'name' => 'Kurir Lokal Favorit',
            'type' => 'local_courier',
            'is_active' => true,
        ]);
        ShippingService::factory()->create(['name' => 'Tidak Aktif', 'is_active' => false]);
        $product = $this->createMarketplaceProduct();
        $this->actingAs($customer)->post(route('cart.items.store', $product->id));

        $this->get(route('marketplace.checkout.index'))
            ->assertOk()
            ->assertSee('Kurir Lokal Favorit')
            ->assertSee(__('marketplace.checkout.shipping_type_local_courier', [], 'id'))
            ->assertSee(__('marketplace.checkout.shipping_cost_not_calculated', [], 'id'))
            ->assertDontSee('Tidak Aktif');

        $this->post(route('marketplace.checkout.store'), $this->checkoutData($shippingService->id))
            ->assertRedirect();

        $order = Order::query()->firstOrFail();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'shipping_service_id' => $shippingService->id,
            'shipping_service_name' => 'Kurir Lokal Favorit',
            'shipping_service_type' => 'local_courier',
        ]);

        $shippingService->delete();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'shipping_service_id' => null,
            'shipping_service_name' => 'Kurir Lokal Favorit',
            'shipping_service_type' => 'local_courier',
        ]);

        $this->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Kurir Lokal Favorit')
            ->assertSee(__('marketplace.checkout.shipping_type_local_courier', [], 'id'))
            ->assertSee(__('marketplace.orders.not_shipped', [], 'id'));
    }

    private function createMarketplaceProduct(): Product
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        FarmerProfile::factory()->for($farmer)->create(['verified_at' => now()]);

        return Product::factory()
            ->for($farmer, 'farmer')
            ->for(Category::factory())
            ->create([
                'status' => 'approved',
                'show_in_marketplace' => true,
                'stock' => 10,
                'price_idr' => 12500,
            ]);
    }

    /** @return array<string, int|string> */
    private function checkoutData(?int $shippingServiceId = null): array
    {
        return [
            'recipient_name' => 'Penerima Uji',
            'phone' => '081234567890',
            'address' => 'Jalan Kebun Nomor 1',
            'notes' => '',
            'payment_method' => 'transfer',
            'payment_term' => 'cash',
            ...($shippingServiceId === null ? [] : ['shipping_service_id' => $shippingServiceId]),
        ];
    }
}
