<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'farmer_id' => fn (array $attributes): int => Product::query()->findOrFail($attributes['product_id'])->farmer_id,
            'product_name' => fake()->words(2, true),
            'unit' => fake()->randomElement(['kg', 'ton']),
            'price' => fake()->numberBetween(1000, 100000),
            'qty' => fake()->numberBetween(1, 100),
            'is_preorder' => false,
            'harvest_date' => null,
        ];
    }
}
