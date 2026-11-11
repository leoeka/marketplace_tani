<?php

namespace Database\Factories;

use App\Models\ShippingService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingService>
 */
class ShippingServiceFactory extends Factory
{
    protected $model = ShippingService::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'type' => fake()->randomElement(['local_courier', 'cold_chain']),
            'is_active' => true,
        ];
    }
}
