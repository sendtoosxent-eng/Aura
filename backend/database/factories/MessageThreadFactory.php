<?php

namespace Database\Factories;

use App\Models\MessageThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MessageThread>
 */
class MessageThreadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'patient_user_id' => User::factory()->state(['account_type' => 'patient']),
            'provider_user_id' => User::factory()->state(['account_type' => 'professional']),
            'subtitle' => 'AfyaLink care',
            'is_automated' => false,
            'last_activity_at' => now(),
        ];
    }
}
