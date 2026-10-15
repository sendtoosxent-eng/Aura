<?php

namespace App\Http\Controllers;

use App\Models\AppointmentBooking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AppointmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'healthcare_provider_id' => [
                'nullable',
                'exists:healthcare_providers,id',
            ],

            'doctor_id' => [
                'nullable',
                'exists:doctors,id',
            ],

            'appointment_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'appointment_time' => [
                'required',
                'date_format:H:i',
            ],

            'appointment_type' => [
                'required',
                'in:in_person',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ]);


        if (
            empty($data['doctor_id']) &&
            empty($data['healthcare_provider_id'])
        ) {
            throw ValidationException::withMessages([
                'booking' =>
                    'Please choose a healthcare provider or doctor.',
            ]);
        }


        $booking =
            AppointmentBooking::create([
                'user_id' =>
                    $request->user()->id,

                'healthcare_provider_id' =>
                    $data['healthcare_provider_id']
                    ?? null,

                'doctor_id' =>
                    $data['doctor_id']
                    ?? null,

                'appointment_date' =>
                    $data['appointment_date'],

                'appointment_time' =>
                    $data['appointment_time'],

                'appointment_type' =>
                    $data['appointment_type'],

                'reason' =>
                    $data['reason'] ?? null,

                'status' => 'pending',
            ]);


        return response()->json([
            'message' =>
                'Your visit request has been submitted.',

            'booking_id' =>
                $booking->id,
        ]);
    }
}