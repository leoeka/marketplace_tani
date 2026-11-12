<?php

namespace Database\Factories;

use App\Models\LoadingPort;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoadingPort>
 */
class LoadingPortFactory extends Factory
{
    protected $model = LoadingPort::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city().' Port',
            'city' => fake()->city(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}