<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Rfq;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rfq>
 */
class RfqFactory extends Factory
{
    public function definition(): array
    {
        $hasQuantity = fake()->boolean();

        return [
            'buyer_name' => fake()->name(),
            'company' => fake()->company(),
            'country' => fake()->country(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'product_id' => null,
            'product_name' => null,
            'quantity' => $hasQuantity ? fake()->numberBetween(1, 100000) : null,
            'quantity_unit' => $hasQuantity ? fake()->randomElement(['kg', 'ton']) : null,
            'wants_sample' => fake()->boolean(),
            'asks_moq' => fake()->boolean(),
            'incoterms' => fake()->randomElement(['FOB', 'CIF', 'EXW', 'TBD']),
            'message' => fake()->optional()->sentence(),
            'status' => 'new',
            'handled_at' => null,
        ];
    }

    public function forProduct(Product $product): static
    {
        return $this->state(fn (): array => [
            'product_id' => $product->id,
            'product_name' => $product->exportDetails?->name_en,
        ]);
    }
}