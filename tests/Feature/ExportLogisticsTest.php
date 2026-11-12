<?php

namespace Tests\Feature;

use App\Models\FreightEstimate;
use App\Models\LoadingPort;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ExportLogisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_english_logistics_page_and_only_active_loading_ports(): void
    {
        $activePort = LoadingPort::factory()->create([
            'name' => 'Active Harbor',
            'city' => 'Active City',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        LoadingPort::factory()->create([
            'name' => 'Inactive Harbor',
            'city' => 'Inactive City',
            'sort_order' => 0,
            'is_active' => false,
        ]);

        $this->get(route('export.logistics.index'))
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr">', false)
            ->assertSee('Sea freight')
            ->assertSee('Air freight')
            ->assertSee('Main loading ports')
            ->assertSee('Active Harbor')
            ->assertDontSee('Inactive Harbor')
            ->assertSee('Logistics');

        $this->assertTrue($activePort->is_active);
    }

    public function test_loading_ports_have_a_friendly_empty_state_and_no_estimate_is_hard_coded(): void
    {
        $this->get(route('export.logistics.index'))
            ->assertOk()
            ->assertSee('Loading port information is not available yet.')
            ->assertSee('These figures are estimates only')
            ->assertSee('Request a quote')
            ->assertDontSee('Indicative estimate')
            ->assertDontSee('Indicative cost: USD');
    }

    /** @return array<string, array{string, string, string, string, string, string, string}> */
    public static function calculatorScenarios(): array
    {
        return [
            'sea freight in tons' => ['sea', 'ton', '1.25', '2.50', '3', '7', '3,125.00'],
            'air freight in kilograms' => ['air', 'kg', '4', '3.25', '6', '6', '13.00'],
        ];
    }

    #[DataProvider('calculatorScenarios')]
    public function test_calculator_shows_admin_entered_transit_and_cost(
        string $mode,
        string $unit,
        string $weight,
        string $rate,
        string $minimum,
        string $maximum,
        string $cost,
    ): void {
        $port = LoadingPort::factory()->create(['name' => 'Calculator Harbor']);
        FreightEstimate::factory()->create([
            'mode' => $mode,
            'destination' => 'Destination Gamma',
            'loading_port_id' => $mode === 'sea' ? $port->id : null,
            'transit_days_min' => (int) $minimum,
            'transit_days_max' => (int) $maximum,
            'rate_usd_per_kg' => $rate,
        ]);
        $query = [
            'mode' => $mode,
            'destination' => 'Destination Gamma',
            'weight' => $weight,
            'unit' => $unit,
        ];

        if ($mode === 'sea') {
            $query['loading_port_id'] = (string) $port->id;
        }

        $response = $this->get(route('export.logistics.index', $query))
            ->assertOk()
            ->assertSee('Indicative estimate')
            ->assertSee('USD '.$cost)
            ->assertSee('These figures are estimates only')
            ->assertSee('Request a quote');

        if ($minimum === $maximum) {
            $response->assertSee($minimum.' days');
        } else {
            $response->assertSee($minimum.' to '.$maximum.' days');
        }
    }

    public function test_calculator_shows_duration_without_cost_when_rate_is_missing(): void
    {
        $port = LoadingPort::factory()->create();
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'destination' => 'Destination Delta',
            'loading_port_id' => $port->id,
            'transit_days_min' => 8,
            'transit_days_max' => 8,
            'rate_usd_per_kg' => null,
        ]);

        $this->get(route('export.logistics.index', [
            'mode' => 'sea',
            'loading_port_id' => $port->id,
            'destination' => 'Destination Delta',
            'weight' => '12',
            'unit' => 'kg',
        ]))
            ->assertOk()
            ->assertSee('8 days')
            ->assertSee('A rate is not available yet. Request a quote from our team.')
            ->assertSee('These figures are estimates only')
            ->assertSee('Request a quote')
            ->assertDontSee('Indicative cost: USD');
    }

    public function test_unmatched_route_shows_friendly_result_and_disclaimer(): void
    {
        $estimatePort = LoadingPort::factory()->create();
        $selectedPort = LoadingPort::factory()->create();
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'destination' => 'Destination Epsilon',
            'loading_port_id' => $estimatePort->id,
        ]);

        $this->get(route('export.logistics.index', [
            'mode' => 'sea',
            'loading_port_id' => $selectedPort->id,
            'destination' => 'Destination Epsilon',
            'weight' => '10',
            'unit' => 'kg',
        ]))
            ->assertOk()
            ->assertSee('We do not have an estimate for this combination yet.')
            ->assertSee('These figures are estimates only')
            ->assertDontSee('Indicative cost: USD');
    }

    public function test_inactive_estimates_are_not_available_to_the_public_calculator(): void
    {
        $port = LoadingPort::factory()->create();
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'destination' => 'Inactive Destination',
            'loading_port_id' => $port->id,
            'is_active' => false,
        ]);

        $this->get(route('export.logistics.index'))
            ->assertOk()
            ->assertDontSee('Inactive Destination');

        $this->get(route('export.logistics.index', [
            'mode' => 'sea',
            'loading_port_id' => $port->id,
            'destination' => 'Inactive Destination',
            'weight' => '10',
            'unit' => 'kg',
        ]))->assertSessionHasErrors('destination');
    }

    public function test_sea_estimates_for_inactive_ports_do_not_offer_a_destination(): void
    {
        $inactivePort = LoadingPort::factory()->create(['is_active' => false]);
        $activePort = LoadingPort::factory()->create(['is_active' => true]);
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'destination' => 'Unavailable Port Destination',
            'loading_port_id' => $inactivePort->id,
            'is_active' => true,
        ]);

        $this->get(route('export.logistics.index'))
            ->assertOk()
            ->assertDontSee('Unavailable Port Destination');

        $this->get(route('export.logistics.index', [
            'mode' => 'sea',
            'loading_port_id' => $activePort->id,
            'destination' => 'Unavailable Port Destination',
            'weight' => '10',
            'unit' => 'kg',
        ]))->assertSessionHasErrors('destination');
    }

    /** @return array<string, array{string, string}> */
    public static function invalidWeights(): array
    {
        return [
            'zero kilograms' => ['0', 'kg'],
            'negative kilograms' => ['-1', 'kg'],
            'over the kilogram limit' => ['1000001', 'kg'],
            'over the ton limit' => ['1001', 'ton'],
        ];
    }

    #[DataProvider('invalidWeights')]
    public function test_invalid_calculator_weight_is_rejected(string $weight, string $unit): void
    {
        $port = LoadingPort::factory()->create();
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'destination' => 'Destination Zeta',
            'loading_port_id' => $port->id,
        ]);

        $this->get(route('export.logistics.index', [
            'mode' => 'sea',
            'loading_port_id' => $port->id,
            'destination' => 'Destination Zeta',
            'weight' => $weight,
            'unit' => $unit,
        ]))->assertSessionHasErrors('weight');

    }

    public function test_public_logistics_output_escapes_admin_entered_html(): void
    {
        $port = LoadingPort::factory()->create([
            'name' => '<script>alert(1)</script>',
            'city' => '<b>City</b>',
        ]);
        FreightEstimate::factory()->create([
            'mode' => 'sea',
            'destination' => '<script>alert(2)</script>',
            'loading_port_id' => $port->id,
        ]);

        $this->get(route('export.logistics.index'))
            ->assertOk()
            ->assertSee('<script>alert(1)</script>')
            ->assertSee('<b>City</b>')
            ->assertSee('<script>alert(2)</script>')
            ->assertDontSee('<script>', false)
            ->assertDontSee('<b>', false);
    }
}