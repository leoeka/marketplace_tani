<?php

namespace Database\Factories;

use App\Models\MarketPrice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketPrice>
 */
class MarketPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'commodity' => ucfirst(fake()->word()),
            'price' => fake()->numberBetween(1000, 100000),
            'unit' => fake()->randomElement(['kg', 'ton']),
            'source' => fake()->company(),
            'recorded_on' => today()->toDateString(),
        ];
    }
}
