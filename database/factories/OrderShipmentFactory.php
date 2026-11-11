<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderShipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderShipment>
 */
class OrderShipmentFactory extends Factory
{
    protected $model = OrderShipment::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'farmer_id' => User::factory()->state(['role' => 'farmer']),
            'tracking_number' => fake()->bothify('??########'),
            'shipped_at' => now(),
        ];
    }
}
