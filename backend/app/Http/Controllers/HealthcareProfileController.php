<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\HealthcareProvider;
use Illuminate\Http\JsonResponse;

class HealthcareProfileController extends Controller
{
    public function provider(HealthcareProvider $provider): JsonResponse
    {
        $provider->load([
            'doctors' => function ($query) {
                $query
                    ->where('is_verified', true)
                    ->orderByDesc('rating');
            },
        ]);

        return response()->json([
            'type' => 'provider',

            'profile' => [
                'id' => $provider->id,

                'name' => $provider->name,

                'provider_type' => ucfirst($provider->provider_type),

                'ownership' => ucfirst(
                    str_replace(
                        '_',
                        ' ',
                        $provider->ownership ?? ''
                    )
                ),

                'district' => $provider->district,

                'town' => $provider->town,

                'address' => $provider->address,

                'phone' => $provider->phone,

                'email' => $provider->email,

                'description' => $provider->description,

                'rating' => (float) $provider->rating,

                'review_count' => $provider->review_count,

                'verified' => $provider->is_verified,

                'open_24_hours' => $provider->open_24_hours,

                'emergency_available' => $provider->emergency_available,

                'image' => $provider->image,

                'services' => $provider->services ?? [],

                'doctors' => $provider->doctors->map(
                    fn ($doctor) => [
                        'id' => $doctor->id,
                        'name' => $doctor->name,
                        'specialty' => $doctor->specialty,
                        'rating' => (float) $doctor->rating,
                        'image' => $doctor->image,
                    ]
                ),
            ],
        ]);
    }

    public function doctor(Doctor $doctor): JsonResponse
    {
        $doctor->load('healthcareProvider');

        return response()->json([
            'type' => 'doctor',

            'profile' => [
                'id' => $doctor->id,

                'name' => $doctor->name,

                'specialty' => $doctor->specialty,

                'district' => $doctor->district,

                'town' => $doctor->town,

                'phone' => $doctor->phone,

                'email' => $doctor->email,

                'bio' => $doctor->bio,

                'rating' => (float) $doctor->rating,

                'review_count' => $doctor->review_count,

                'verified' => $doctor->is_verified,

                'image' => $doctor->image,

                'facility' => $doctor->healthcareProvider
                        ? [
                            'id' => $doctor
                                ->healthcareProvider
                                ->id,

                            'name' => $doctor
                                ->healthcareProvider
                                ->name,

                            'district' => $doctor
                                ->healthcareProvider
                                ->district,
                        ]
                        : null,
            ],
        ]);
    }
}
