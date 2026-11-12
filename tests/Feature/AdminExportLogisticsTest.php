<?php

namespace Tests\Feature;

use App\Models\FreightEstimate;
use App\Models\LoadingPort;
use App\Models\User;
use Database\Seeders\LoadingPortSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminExportLogisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_loading_port_seeder_is_idempotent_and_does_not_create_estimates(): void
    {
        $this->seed(LoadingPortSeeder::class);
        $this->seed(LoadingPortSeeder::class);

        $this->assertDatabaseCount('loading_ports', 3);
        $this->assertDatabaseCount('freight_estimates', 0);
        $this->assertDatabaseHas('loading_ports', ['name' => 'Tanjung Priok', 'city' => 'Jakarta']);
        $this->assertDatabaseHas('loading_ports', ['name' => 'Tanjung Perak', 'city' => 'Surabaya']);
        $this->assertDatabaseHas('loading_ports', ['name' => 'Benoa', 'city' => 'Bali']);
    }

    public function test_admin_can_create_update_and_delete_loading_ports(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.loading-ports.store'), [
            'name' => 'Harbor Alpha',
            'city' => 'City Alpha',
            'is_active' => '1',
            'sort_order' => '4',
        ])->assertRedirect(route('admin.loading-ports.index'));

        $port = LoadingPort::query()->where('name', 'Harbor Alpha')->firstOrFail();
        $this->assertSame('City Alpha', $port->city);
        $this->assertTrue($port->is_active);
        $this->assertSame(4, $port->sort_order);

        $this->put(route('admin.loading-ports.update', $port), [
            'name' => 'Harbor Beta',
            'city' => 'City Beta',
            'is_active' => '0',
            'sort_order' => '2',
        ])->assertRedirect(route('admin.loading-ports.index'));

        $port->refresh();
        $this->assertSame('Harbor Beta', $port->name);
        $this->assertSame('City Beta', $port->city);
        $this->assertFalse($port->is_active);
        $this->assertSame(2, $port->sort_order);

        $this->delete(route('admin.loading-ports.destroy', $port))
            ->assertRedirect(route('admin.loading-ports.index'));

        $this->assertDatabaseMissing('loading_ports', ['id' => $port->id]);
    }

    public function test_admin_cannot_delete_a_loading_port_used_by_an_estimate(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $port = LoadingPort::factory()->create();
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'loading_port_id' => $port->id,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.loading-ports.destroy', $port))
            ->assertRedirect(route('admin.loading-ports.index'))
            ->assertSessionHas('error', 'Pelabuhan tidak dapat dihapus karena masih digunakan oleh estimasi pengiriman.');

        $this->assertDatabaseHas('loading_ports', ['id' => $port->id]);
    }

    public function test_admin_can_create_update_filter_and_delete_freight_estimates(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $port = LoadingPort::factory()->create();

        $this->actingAs($admin)->post(route('admin.freight-estimates.store'), [
            'mode' => 'sea',
            'destination' => 'Destination Alpha',
            'loading_port_id' => $port->id,
            'transit_days_min' => '12',
            'transit_days_max' => '18',
            'rate_usd_per_kg' => '2.50',
            'note' => 'Admin-entered estimate',
            'is_active' => '1',
        ])->assertRedirect(route('admin.freight-estimates.index'));

        $estimate = FreightEstimate::query()->firstOrFail();
        $this->assertSame('sea', $estimate->mode);
        $this->assertSame($port->id, $estimate->loading_port_id);
        $this->assertSame('2.50', $estimate->rate_usd_per_kg);

        $this->put(route('admin.freight-estimates.update', $estimate), [
            'mode' => 'air',
            'destination' => 'Destination Beta',
            'loading_port_id' => '',
            'transit_days_min' => '5',
            'transit_days_max' => '5',
            'rate_usd_per_kg' => '',
            'note' => '',
            'is_active' => '0',
        ])->assertRedirect(route('admin.freight-estimates.index'));

        $estimate->refresh();
        $this->assertSame('air', $estimate->mode);
        $this->assertNull($estimate->loading_port_id);
        $this->assertSame('Destination Beta', $estimate->destination);
        $this->assertNull($estimate->rate_usd_per_kg);
        $this->assertFalse($estimate->is_active);

        $this->get(route('admin.freight-estimates.index', ['mode' => 'air']))
            ->assertOk()
            ->assertSee('Destination Beta')
            ->assertDontSee('Destination Alpha');

        $this->delete(route('admin.freight-estimates.destroy', $estimate))
            ->assertRedirect(route('admin.freight-estimates.index'));

        $this->assertDatabaseMissing('freight_estimates', ['id' => $estimate->id]);
    }

    /** @return array<string, array{array<string, string>, string, bool}> */
    public static function invalidEstimatePayloads(): array
    {
        return [
            'maximum below minimum' => [['mode' => 'sea', 'transit_days_min' => '15', 'transit_days_max' => '10'], 'transit_days_max', false],
            'sea without port' => [['mode' => 'sea', 'loading_port_id' => ''], 'loading_port_id', false],
            'air with port' => [['mode' => 'air'], 'loading_port_id', false],
            'inactive port' => [['mode' => 'sea', 'loading_port_id' => 'inactive'], 'loading_port_id', true],
            'negative rate' => [['rate_usd_per_kg' => '-0.01'], 'rate_usd_per_kg', false],
            'empty destination' => [['destination' => ''], 'destination', false],
        ];
    }

    /** @param array<string, string> $overrides */
    #[DataProvider('invalidEstimatePayloads')]
    public function test_invalid_estimate_fields_are_rejected(
        array $overrides,
        string $errorField,
        bool $useInactivePort,
    ): void {
        $admin = User::factory()->create(['role' => 'admin']);
        $activePort = LoadingPort::factory()->create();
        $inactivePort = LoadingPort::factory()->create(['is_active' => false]);
        $payload = [
            'mode' => 'sea',
            'destination' => 'Destination Alpha',
            'loading_port_id' => $activePort->id,
            'transit_days_min' => '5',
            'transit_days_max' => '8',
            'rate_usd_per_kg' => '1.25',
            'note' => '',
            'is_active' => '1',
        ];

        foreach ($overrides as $key => $value) {
            $payload[$key] = $value === 'inactive' ? (string) $inactivePort->id : $value;
        }

        if ($useInactivePort) {
            $payload['loading_port_id'] = (string) $inactivePort->id;
        }

        $this->actingAs($admin)
            ->from(route('admin.freight-estimates.create'))
            ->post(route('admin.freight-estimates.store'), $payload)
            ->assertSessionHasErrors($errorField);

        $this->assertDatabaseCount('freight_estimates', 0);
    }

    public function test_guest_is_redirected_and_farmer_and_customer_are_forbidden_from_logistics_admin(): void
    {
        $this->get(route('admin.loading-ports.index'))->assertRedirect(route('login'));

        foreach (['farmer', 'customer'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]))
                ->get(route('admin.loading-ports.index'))
                ->assertForbidden();
        }
    }
}