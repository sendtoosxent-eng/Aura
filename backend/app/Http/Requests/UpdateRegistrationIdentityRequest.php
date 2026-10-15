<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRegistrationIdentityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->registration_status === 'draft'
            && $this->user()->account_type === $this->input('application_type');
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
            'application_type' => ['required', Rule::in(['facility', 'professional'])],
            'healthcare_provider_id' => [
                Rule::requiredIf($this->input('application_type') === 'professional'),
                'nullable',
                Rule::exists('healthcare_providers', 'id')->where('is_verified', true),
            ],
            'first_name' => ['required', 'string', 'max:80'],
            'middle_name' => ['nullable', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'date_of_birth' => ['required', 'date', 'before_or_equal:'.now()->subYears(18)->toDateString()],
            'sex' => ['required', Rule::in(['male', 'female'])],
            'nationality' => ['required', 'string', 'max:80'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'phone' => ['required', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user)],
            'nin' => ['required', 'string', 'size:14', 'regex:/^C[MF][A-Z0-9]{12}$/', Rule::unique('users', 'nin')->ignore($user)],
        ];
    }
}
