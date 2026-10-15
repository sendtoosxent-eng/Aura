<?php

namespace Database\Seeders;

use App\Models\AppointmentBooking;
use App\Models\HealthcareProvider;
use App\Models\ProviderUnavailableDate;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentAvailabilityDemoSeeder extends Seeder
{
    public function run(): void
    {
        $facility = HealthcareProvider::query()
            ->where('name', 'Kampala Family Medical Centre')
            ->first();
        $patient = User::query()->where('email', 'patient@afyalink.test')->first();

        if (! $facility || ! $patient) {
            $this->command?->warn('Run MobileDemoSeeder first to create the demo facility and patient.');

            return;
        }

        foreach ([
            [now()->addDays(4)->toDateString(), 'Staff training'],
            [now()->addDays(9)->toDateString(), 'Public holiday'],
            [now()->addDays(15)->toDateString(), 'Facility maintenance'],
        ] as [$date, $reason]) {
            ProviderUnavailableDate::query()->updateOrCreate(
                ['healthcare_provider_id' => $facility->id, 'unavailable_date' => $date],
                ['reason' => $reason],
            );
        }

        AppointmentBooking::query()->updateOrCreate(
            [
                'user_id' => $patient->id,
                'healthcare_provider_id' => $facility->id,
                'appointment_date' => now()->addDays(2)->toDateString(),
                'appointment_time' => '10:30',
            ],
            [
                'appointment_type' => 'in_person',
                'service' => 'General Consultation',
                'reason' => 'Recurring headaches',
                'status' => 'pending',
            ],
        );

        $this->command?->info('Demo appointment request and provider closed dates created.');
    }
}
