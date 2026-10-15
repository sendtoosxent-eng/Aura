<?php

namespace Database\Seeders;

use App\Models\ChatMessage;
use App\Models\Doctor;
use App\Models\HealthcareProvider;
use App\Models\MessageThread;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Database\Seeder;

class MobileAppDataSeeder extends Seeder
{
    public function run(): void
    {
        $patient = User::query()->where('phone', '+256700555666')->firstOrFail();
        $facilityUser = User::query()->where('phone', '+256700111222')->firstOrFail();
        $professionalUser = User::query()->where('phone', '+256700333444')->firstOrFail();
        $facility = HealthcareProvider::query()->where('name', 'Kampala Family Medical Centre')->firstOrFail();
        $doctor = Doctor::query()->where('email', 'doctor@afyalink.test')->first();

        $careThread = MessageThread::query()->updateOrCreate(
            ['patient_user_id' => $patient->id, 'title' => 'AfyaLink Care Assistant'],
            ['subtitle' => 'Automated health reminders', 'is_automated' => true, 'last_activity_at' => now()->subMinutes(15)],
        );
        $providerThread = MessageThread::query()->updateOrCreate(
            ['patient_user_id' => $patient->id, 'provider_user_id' => $professionalUser->id],
            [
                'healthcare_provider_id' => $facility->id,
                'doctor_id' => $doctor?->id,
                'subtitle' => 'General Medicine · Kampala Family Medical Centre',
                'is_automated' => false,
                'last_activity_at' => now()->subMinutes(5),
            ],
        );
        $facilityThread = MessageThread::query()->updateOrCreate(
            ['patient_user_id' => $patient->id, 'provider_user_id' => $facilityUser->id],
            [
                'healthcare_provider_id' => $facility->id,
                'subtitle' => 'Patient support desk',
                'is_automated' => false,
                'last_activity_at' => now()->subDay(),
            ],
        );

        $this->message($careThread, null, 'automated', 'Good morning, Musa. Remember to take your prescribed medicine with food.', now()->subMinutes(15));
        $this->message($providerThread, $patient, 'user', 'Hello Doctor, the fever reduced after the medicine.', now()->subHour());
        $this->message($providerThread, $professionalUser, 'provider', 'That is encouraging. Please continue monitoring your temperature.', now()->subMinutes(5));
        $this->message($facilityThread, $facilityUser, 'provider', 'Your laboratory results are ready for review.', now()->subDay());

        $this->notification($patient, 'Welcome to AfyaLink', 'Your care messages and account updates will appear here.', 'system', 'hand.wave.fill', now()->subWeek());
        $this->notification($patient, 'Appointment reminder', 'Your next consultation is approaching. Open appointments for details.', 'alerts', 'calendar.badge.clock', now()->subHour());
        $this->notification($professionalUser, 'New patient message', 'Musa Balondemu sent you a care update.', 'updates', 'message.fill', now()->subMinutes(5));
        $this->notification($facilityUser, 'Patient support update', 'A patient conversation is waiting for your response.', 'updates', 'message.fill', now()->subDay());
    }

    private function message(MessageThread $thread, ?User $sender, string $senderType, string $body, mixed $sentAt): void
    {
        ChatMessage::query()->firstOrCreate(
            ['message_thread_id' => $thread->id, 'body' => $body],
            ['sender_user_id' => $sender?->id, 'sender_type' => $senderType, 'sent_at' => $sentAt],
        );
    }

    private function notification(User $user, string $title, string $body, string $category, string $icon, mixed $createdAt): void
    {
        UserNotification::query()->firstOrCreate(
            ['user_id' => $user->id, 'title' => $title],
            ['body' => $body, 'category' => $category, 'icon' => $icon, 'created_at' => $createdAt, 'updated_at' => $createdAt],
        );
    }
}
