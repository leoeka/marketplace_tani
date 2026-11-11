<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductExportDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductExportDetail>
 */
class ProductExportDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name_en' => fake()->words(2, true),
            'description_en' => fake()->paragraph(),
            'hs_code' => fake()->numerify('####.##'),
            'moisture_percent' => fake()->randomFloat(2, 0, 100),
            'size_spec' => fake()->optional()->sentence(4),
            'monthly_capacity' => fake()->numberBetween(1, 100000),
            'monthly_capacity_unit' => fake()->randomElement(['kg', 'ton']),
        ];
    }
}