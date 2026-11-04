<?php

namespace Database\Factories;

use App\Models\FarmerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FarmerProfile>
 */
class FarmerProfileFactory extends Factory
{
    public function definition(): array
    {
        $shopName = fake()->unique()->company();

        return [
            'user_id' => User::factory()->state(['role' => 'farmer']),
            'shop_name' => $shopName,
            'slug' => Str::slug($shopName),
            'group_name' => fake()->optional()->company(),
            'region' => fake()->city().', '.fake()->state(),
            'story' => fake()->optional()->paragraph(),
            'verified_at' => fake()->optional()->dateTimeBetween('-1 year'),
        ];
    }
}