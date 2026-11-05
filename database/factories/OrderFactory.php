<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::factory()->state(['role' => 'customer']),
            'payment_method' => fake()->randomElement(['transfer', 'ewallet', 'qris']),
            'payment_term' => 'cash',
            'payment_status' => 'unpaid',
            'total' => fake()->numberBetween(1000, 1000000),
            'shipping_address' => fake()->address(),
        ];
    }
}
