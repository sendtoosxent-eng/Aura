<?php

namespace Database\Factories;

use App\Models\PushDevice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PushDevice>
 */
class PushDeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'token_hash' => hash('sha256', $token = fake()->sha256()),
            'token' => $token,
            'platform' => 'ios',
            'environment' => 'development',
            'app_bundle_id' => 'ug.afyalink.ios',
            'last_seen_at' => now(),
        ];
    }
}
