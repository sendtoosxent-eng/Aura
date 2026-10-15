<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreRegistrationApplicationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->hasVerifiedEmail() === true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'application_type' => ['required', Rule::in(['facility', 'professional'])],
            'healthcare_provider_id' => [
                Rule::requiredIf($this->input('application_type') === 'professional'),
                'nullable',
                'integer',
                'exists:healthcare_providers,id',
            ],
            'regulatory' => ['required', 'array'],
            'regulatory.cadre' => ['required_if:application_type,professional', 'nullable', 'string', 'max:100'],
            'regulatory.specialty' => ['nullable', 'string', 'max:150'],
            'regulatory.regulatory_body' => ['required_if:application_type,professional', 'nullable', 'string', 'max:180'],
            'regulatory.registration_number' => ['required_if:application_type,professional', 'nullable', 'string', 'max:100'],
            'regulatory.licence_number' => ['required', 'string', 'max:100'],
            'regulatory.licence_expiry' => ['required', 'date', 'after_or_equal:today'],
            'regulatory.years_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'regulatory.name' => ['required_if:application_type,facility', 'nullable', 'string', 'max:180'],
            'regulatory.provider_type' => ['required_if:application_type,facility', 'nullable', 'string', 'max:80'],
            'regulatory.ownership' => ['required_if:application_type,facility', 'nullable', 'string', 'max:80'],
            'regulatory.district' => ['required_if:application_type,facility', 'nullable', 'string', 'max:100'],
            'regulatory.town' => ['required_if:application_type,facility', 'nullable', 'string', 'max:100'],
            'regulatory.address' => ['required_if:application_type,facility', 'nullable', 'string', 'max:255'],
            'regulatory.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'regulatory.longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'regulatory.in_charge' => ['nullable', 'string', 'max:180'],
            'regulatory.phone' => ['required_if:application_type,facility', 'nullable', 'string', 'max:30'],
            'regulatory.email' => ['nullable', 'email', 'max:150'],
            'regulatory.open_24_hours' => ['nullable', 'boolean'],
            'regulatory.emergency_available' => ['nullable', 'boolean'],
            'regulatory.services' => ['nullable', 'array', 'max:30'],
            'regulatory.services.*' => ['string', 'max:100'],
            'regulatory.services_other' => ['nullable', 'string', 'max:1000'],
            'documents' => ['required', 'array'],
            'documents.*' => [File::types(['pdf', 'jpg', 'jpeg', 'png', 'webp'])->max('5mb')],
            'documents.national_id' => ['required_if:application_type,professional'],
            'documents.practising_licence' => ['required_if:application_type,professional'],
            'documents.academic_certificate' => ['required_if:application_type,professional'],
            'documents.operating_licence' => ['required_if:application_type,facility'],
            'documents.registration_certificate' => ['required_if:application_type,facility'],
            'documents.responsible_officer_id' => ['required_if:application_type,facility'],
            'documents.facility_photo' => ['nullable', File::image()->max('5mb')],
            'declaration' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'documents.*.max' => 'Each document must be 5 MB or smaller.',
            'declaration.accepted' => 'Please confirm the declaration before submitting.',
        ];
    }
}
