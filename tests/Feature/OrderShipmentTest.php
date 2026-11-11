<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderShipment;
use App\Models\Product;
use App\Models\ShippingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderShipmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_farmer_can_save_a_tracking_number_for_an_order_that_contains_their_items(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        $order = $this->createOrderWithFarmers([$farmer]);
        $this->travelTo(now()->startOfSecond());
        $shippedAt = now()->toDateTimeString();

        $this->actingAs($farmer)
            ->get(route('farmer.orders.show', $order))
            ->assertOk()
            ->assertSee(__('farmer.orders.not_shipped', [], 'id'));

        $this->actingAs($farmer)
            ->post(route('farmer.orders.shipments.store', $order), ['tracking_number' => 'RESI-LOKAL-1'])
            ->assertRedirect(route('farmer.orders.show', $order));

        $shipment = OrderShipment::query()->where('order_id', $order->id)->firstOrFail();
        $this->assertSame('RESI-LOKAL-1', $shipment->tracking_number);
        $this->assertSame($shippedAt, $shipment->shipped_at->toDateTimeString());
    }

    public function test_farmer_cannot_save_a_tracking_number_for_an_order_without_their_items(): void
    {
        $ownerFarmer = User::factory()->create(['role' => 'farmer']);
        $otherFarmer = User::factory()->create(['role' => 'farmer']);
        $order = $this->createOrderWithFarmers([$ownerFarmer]);

        $this->actingAs($otherFarmer)
            ->post(route('farmer.orders.shipments.store', $order), ['tracking_number' => 'RESI-TERLARANG'])
            ->assertForbidden();

        $this->assertDatabaseCount('order_shipments', 0);
    }

    public function test_each_farmer_in_a_multi_farmer_order_has_a_separate_shipment_and_all_order_views_show_it(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $firstFarmer = User::factory()->create(['role' => 'farmer']);
        $secondFarmer = User::factory()->create(['role' => 'farmer']);
        $shippingService = ShippingService::factory()->create([
            'name' => 'Cold Chain Utama',
            'type' => 'cold_chain',
        ]);
        $order = $this->createOrderWithFarmers([$firstFarmer, $secondFarmer], $customer, $shippingService);
        $this->travelTo(now()->startOfSecond());
        $shipmentDate = now()->format('d/m/Y');

        $this->actingAs($firstFarmer)
            ->post(route('farmer.orders.shipments.store', $order), ['tracking_number' => 'RESI-PETANI-A'])
            ->assertRedirect();
        $this->actingAs($secondFarmer)
            ->post(route('farmer.orders.shipments.store', $order), ['tracking_number' => 'RESI-PETANI-B'])
            ->assertRedirect();

        $this->assertDatabaseHas('order_shipments', [
            'order_id' => $order->id,
            'farmer_id' => $firstFarmer->id,
            'tracking_number' => 'RESI-PETANI-A',
        ]);
        $this->assertDatabaseHas('order_shipments', [
            'order_id' => $order->id,
            'farmer_id' => $secondFarmer->id,
            'tracking_number' => 'RESI-PETANI-B',
        ]);
        $this->assertDatabaseCount('order_shipments', 2);

        $this->actingAs($customer)
            ->get(route('customer.orders.show', $order))
            ->assertOk()
            ->assertSee('Cold Chain Utama')
            ->assertSee(__('marketplace.checkout.shipping_type_cold_chain', [], 'id'))
            ->assertSee('RESI-PETANI-A')
            ->assertSee('RESI-PETANI-B')
            ->assertSee($shipmentDate);

        $this->actingAs($firstFarmer)
            ->get(route('farmer.orders.show', $order))
            ->assertOk()
            ->assertSee('Cold Chain Utama')
            ->assertSee('RESI-PETANI-A')
            ->assertDontSee('RESI-PETANI-B');

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee('Cold Chain Utama')
            ->assertSee('RESI-PETANI-A')
            ->assertSee('RESI-PETANI-B');
    }

    public function test_updating_a_tracking_number_keeps_the_original_shipped_at_timestamp(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        $order = $this->createOrderWithFarmers([$farmer]);
        $this->travelTo(now()->startOfSecond());

        $this->actingAs($farmer)
            ->post(route('farmer.orders.shipments.store', $order), ['tracking_number' => 'RESI-AWAL'])
            ->assertRedirect();
        $shippedAt = OrderShipment::query()->where('order_id', $order->id)->value('shipped_at');

        $this->travel(2)->seconds();
        $this->post(route('farmer.orders.shipments.store', $order), ['tracking_number' => 'RESI-UBAH'])
            ->assertRedirect();

        $shipment = OrderShipment::query()->where('order_id', $order->id)->firstOrFail();
        $this->assertSame('RESI-UBAH', $shipment->tracking_number);
        $this->assertSame(
            $shippedAt->toDateTimeString(),
            \Illuminate\Support\Carbon::parse($shipment->getRawOriginal('shipped_at'))->toDateTimeString()
        );
    }

    /** @param array<int, User> $farmers */
    private function createOrderWithFarmers(array $farmers, ?User $customer = null, ?ShippingService $shippingService = null): Order
    {
        $customer ??= User::factory()->create(['role' => 'customer']);
        $order = Order::factory()->for($customer, 'customer')->create([
            'shipping_service_id' => $shippingService?->id,
            'shipping_service_name' => $shippingService?->name,
            'shipping_service_type' => $shippingService?->type,
        ]);
        $category = Category::factory()->create();

        foreach ($farmers as $farmer) {
            $product = Product::factory()
                ->for($farmer, 'farmer')
                ->for($category)
                ->create();
            OrderItem::factory()
                ->for($order)
                ->for($product)
                ->for($farmer, 'farmer')
                ->create();
        }

        return $order;
    }
}
