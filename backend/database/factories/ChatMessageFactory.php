<?php

namespace Database\Factories;

use App\Models\ChatMessage;
use App\Models\MessageThread;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ChatMessage>
 */
class ChatMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message_thread_id' => MessageThread::factory(),
            'sender_user_id' => null,
            'sender_type' => 'automated',
            'body' => fake()->sentence(),
            'read_at' => null,
            'sent_at' => now(),
        ];
    }
}
