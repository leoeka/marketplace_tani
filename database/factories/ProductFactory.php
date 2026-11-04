<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'farmer_id' => User::factory()->state(['role' => 'farmer']),
            'category_id' => Category::factory(),
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'description' => fake()->paragraph(),
            'unit' => fake()->randomElement(['kg', 'ton']),
            'price_idr' => fake()->numberBetween(1000, 100000),
            'stock' => fake()->numberBetween(1, 1000),
            'status' => 'draft',
            'rejection_note' => null,
            'show_in_marketplace' => false,
            'show_in_export' => false,
        ];
    }
}