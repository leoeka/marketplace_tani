<?php

namespace Tests\Feature;

use App\Models\ShippingService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_delete_shipping_services(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $payload = [
            'name' => 'Kurir Segar',
            'type' => 'local_courier',
            'is_active' => 1,
        ];

        $this->actingAs($admin)
            ->post(route('admin.shipping-services.store'), $payload)
            ->assertRedirect(route('admin.shipping-services.index'));

        $shippingService = ShippingService::query()->firstOrFail();
        $this->assertDatabaseHas('shipping_services', [
            'id' => $shippingService->id,
            'name' => 'Kurir Segar',
            'type' => 'local_courier',
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Ekspedisi Rantai Dingin',
            'type' => 'cold_chain',
            'is_active' => 0,
        ];
        $this->put(route('admin.shipping-services.update', $shippingService), $payload)
            ->assertRedirect(route('admin.shipping-services.index'));

        $this->assertDatabaseHas('shipping_services', [
            'id' => $shippingService->id,
            'name' => 'Ekspedisi Rantai Dingin',
            'type' => 'cold_chain',
            'is_active' => false,
        ]);

        $this->delete(route('admin.shipping-services.destroy', $shippingService))
            ->assertRedirect(route('admin.shipping-services.index'));

        $this->assertDatabaseMissing('shipping_services', ['id' => $shippingService->id]);
    }

    public function test_farmer_and_customer_cannot_open_shipping_service_management(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);
        $customer = User::factory()->create(['role' => 'customer']);

        $this->actingAs($farmer)
            ->get(route('admin.shipping-services.index'))
            ->assertForbidden();

        $this->actingAs($customer)
            ->get(route('admin.shipping-services.index'))
            ->assertForbidden();
    }
}
