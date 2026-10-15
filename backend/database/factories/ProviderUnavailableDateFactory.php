<?php

namespace Database\Factories;

use App\Models\HealthcareProvider;
use App\Models\ProviderUnavailableDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProviderUnavailableDate>
 */
class ProviderUnavailableDateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'healthcare_provider_id' => HealthcareProvider::factory(),
            'unavailable_date' => fake()->dateTimeBetween('+1 day', '+2 months'),
            'reason' => fake()->randomElement(['Public holiday', 'Staff training', 'Fully booked']),
        ];
    }
}
