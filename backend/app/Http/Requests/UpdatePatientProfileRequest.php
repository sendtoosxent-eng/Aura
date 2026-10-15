<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->account_type === 'patient';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();

        return [
            'name' => ['required', 'string', 'max:160'],
            'phone' => ['required', 'string', 'regex:/^\+2567\d{8}$/', Rule::unique('users', 'phone')->ignore($user)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'nin' => ['required', 'string', 'size:14', 'regex:/^C[MF][0-9]{8}[A-Z0-9]{4}$/', Rule::unique('users', 'nin')->ignore($user)],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'gender' => ['nullable', 'string', 'in:Male,Female,Other,Prefer not to say'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'cover_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'blood_group' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'systolic' => ['nullable', 'integer', 'between:70,250'],
            'diastolic' => ['nullable', 'integer', 'between:40,150', 'lt:systolic'],
            'heart_rate' => ['nullable', 'integer', 'between:30,220'],
            'height_cm' => ['nullable', 'numeric', 'between:50,250'],
            'weight_kg' => ['nullable', 'numeric', 'between:2,400'],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'chronic_conditions' => ['nullable', 'string', 'max:2000'],
            'current_medications' => ['nullable', 'string', 'max:2000'],
            'emergency_contact_name' => ['required', 'string', 'max:160'],
            'emergency_contact_relationship' => ['required', 'string', 'max:80'],
            'emergency_contact_phone' => ['required', 'string', 'regex:/^\+2567\d{8}$/', 'different:phone'],
            'health_data_consent' => ['required', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'phone.regex' => 'Enter a valid Ugandan mobile number.',
            'emergency_contact_phone.regex' => 'Enter a valid Ugandan emergency contact number.',
        ];
    }
}
