<?php

namespace Database\Factories;

use App\Models\Certificate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Certificate>
 */
class CertificateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'type' => 'other',
            'issuer' => fake()->optional()->company(),
            'certificate_number' => fake()->optional()->bothify('CERT-####'),
            'expires_at' => fake()->boolean(60)
                ? fake()->dateTimeBetween('+1 month', '+2 years')->format('Y-m-d')
                : null,
            'file_path' => 'certificates/'.fake()->uuid().'.pdf',
        ];
    }
}