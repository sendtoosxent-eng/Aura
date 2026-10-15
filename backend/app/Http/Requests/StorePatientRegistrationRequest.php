<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StorePatientRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'regex:/^\+2567\d{8}$/', 'unique:users,phone'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'nin' => ['required', 'string', 'size:14', 'regex:/^C[MF][0-9]{8}[A-Z0-9]{4}$/', 'unique:users,nin'],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'systolic' => ['nullable', 'integer', 'between:70,250'],
            'diastolic' => ['nullable', 'integer', 'between:40,150', 'lt:systolic'],
            'heart_rate' => ['nullable', 'integer', 'between:30,220'],
            'height_cm' => ['nullable', 'numeric', 'between:50,250'],
            'weight_kg' => ['nullable', 'numeric', 'between:2,400'],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'chronic_conditions' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['required', 'string', 'max:160'],
            'emergency_contact_relationship' => ['required', 'string', 'max:80'],
            'emergency_contact_phone' => ['required', 'string', 'regex:/^\+2567\d{8}$/', 'different:phone'],
            'health_data_consent' => ['accepted'],
            'device_name' => ['required', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid Ugandan mobile number.',
            'emergency_contact_phone.regex' => 'Enter a valid Ugandan emergency contact number.',
            'health_data_consent.accepted' => 'Please allow emergency access to the health information you provided.',
        ];
    }
}
