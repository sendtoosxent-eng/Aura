<?php

namespace Database\Factories;

use App\Models\HealthcareProvider;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HealthcareProvider>
 */
class HealthcareProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company().' Clinic',
            'provider_type' => 'clinic',
            'district' => 'Kampala',
            'is_verified' => true,
        ];
    }
}
