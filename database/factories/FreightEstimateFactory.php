<?php

namespace Database\Factories;

use App\Models\FreightEstimate;
use App\Models\LoadingPort;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FreightEstimate>
 */
class FreightEstimateFactory extends Factory
{
    protected $model = FreightEstimate::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mode = fake()->randomElement(['sea', 'air']);

        return [
            'mode' => $mode,
            'destination' => fake()->country(),
            'loading_port_id' => $mode === 'sea' ? LoadingPort::factory() : null,
            'transit_days_min' => 1,
            'transit_days_max' => 1,
            'rate_usd_per_kg' => null,
            'note' => null,
            'is_active' => true,
        ];
    }
}