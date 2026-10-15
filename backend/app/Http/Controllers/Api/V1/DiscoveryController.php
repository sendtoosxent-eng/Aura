<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\HealthcareProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DiscoveryController extends Controller
{
    public function providers(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'type' => ['nullable', Rule::in(['hospital', 'clinic', 'pharmacy', 'laboratory', 'ambulance'])],
            'district' => ['nullable', 'string', 'max:100'],
            'service' => ['nullable', 'string', 'max:100'],
            'emergency' => ['nullable', 'boolean'],
            'open_24_hours' => ['nullable', 'boolean'],
            'min_rating' => ['nullable', 'numeric', 'between:0,5'],
            'map_only' => ['nullable', 'boolean'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $query = trim($data['q'] ?? '');
        $providers = HealthcareProvider::query()
            ->where('is_verified', true)
            ->when($query !== '', fn ($builder) => $builder->where(function ($nested) use ($query) {
                $nested->where('name', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('town', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%")
                    ->orWhere('services', 'like', "%{$query}%");
            }))
            ->when($data['type'] ?? null, fn ($builder, $type) => $builder->where('provider_type', $type))
            ->when($data['district'] ?? null, fn ($builder, $district) => $builder->where('district', $district))
            ->when($data['service'] ?? null, fn ($builder, $service) => $builder->where('services', 'like', "%{$service}%"))
            ->when($request->boolean('emergency'), fn ($builder) => $builder->where('emergency_available', true))
            ->when($request->boolean('open_24_hours'), fn ($builder) => $builder->where('open_24_hours', true))
            ->when(isset($data['min_rating']), fn ($builder) => $builder->where('rating', '>=', $data['min_rating']))
            ->when($request->boolean('map_only'), fn ($builder) => $builder->whereNotNull('latitude')->whereNotNull('longitude'))
            ->withCount(['doctors' => fn ($builder) => $builder->where('is_verified', true)])
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->paginate($data['per_page'] ?? 20);

        $providers->through(fn (HealthcareProvider $provider) => $this->providerPayload($provider));

        return response()->json($providers);
    }

    public function provider(int $provider): JsonResponse
    {
        $record = HealthcareProvider::query()
            ->where('is_verified', true)
            ->with(['doctors' => fn ($builder) => $builder->where('is_verified', true)->orderByDesc('rating')])
            ->findOrFail($provider);

        return response()->json([
            'data' => $this->providerPayload($record) + [
                'doctors' => $record->doctors->map(fn (Doctor $doctor) => $this->doctorPayload($doctor)),
            ],
        ]);
    }

    public function doctors(Request $request): JsonResponse
    {
        $data = $request->validate([
            'q' => ['nullable', 'string', 'max:150'],
            'specialty' => ['nullable', 'string', 'max:150'],
            'district' => ['nullable', 'string', 'max:100'],
            'provider_id' => ['nullable', 'integer', 'exists:healthcare_providers,id'],
            'per_page' => ['nullable', 'integer', 'between:1,50'],
        ]);

        $query = trim($data['q'] ?? '');
        $doctors = Doctor::query()
            ->where('is_verified', true)
            ->with('healthcareProvider:id,name,address,district,town,latitude,longitude,phone')
            ->when($query !== '', fn ($builder) => $builder->where(function ($nested) use ($query) {
                $nested->where('name', 'like', "%{$query}%")
                    ->orWhere('specialty', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('town', 'like', "%{$query}%");
            }))
            ->when($data['specialty'] ?? null, fn ($builder, $specialty) => $builder->where('specialty', 'like', "%{$specialty}%"))
            ->when($data['district'] ?? null, fn ($builder, $district) => $builder->where('district', $district))
            ->when($data['provider_id'] ?? null, fn ($builder, $providerId) => $builder->where('healthcare_provider_id', $providerId))
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->paginate($data['per_page'] ?? 20);

        $doctors->through(fn (Doctor $doctor) => $this->doctorPayload($doctor));

        return response()->json($doctors);
    }

    public function doctor(int $doctor): JsonResponse
    {
        $record = Doctor::query()
            ->where('is_verified', true)
            ->with('healthcareProvider:id,name,address,district,town,latitude,longitude,phone')
            ->findOrFail($doctor);

        return response()->json(['data' => $this->doctorPayload($record)]);
    }

    /** @return array<string, mixed> */
    private function providerPayload(HealthcareProvider $provider): array
    {
        return [
            'id' => $provider->id,
            'name' => $provider->name,
            'provider_type' => $provider->provider_type,
            'ownership' => $provider->ownership,
            'district' => $provider->district,
            'town' => $provider->town,
            'address' => $provider->address,
            'phone' => $provider->phone,
            'email' => $provider->email,
            'description' => $provider->description,
            'latitude' => $provider->latitude,
            'longitude' => $provider->longitude,
            'rating' => (float) $provider->rating,
            'review_count' => $provider->review_count,
            'verified' => (bool) $provider->is_verified,
            'open_24_hours' => (bool) $provider->open_24_hours,
            'emergency_available' => (bool) $provider->emergency_available,
            'image' => $provider->image,
            'services' => $provider->services ?? [],
            'doctor_count' => $provider->doctors_count ?? null,
        ];
    }

    /** @return array<string, mixed> */
    private function doctorPayload(Doctor $doctor): array
    {
        $facility = $doctor->healthcareProvider;

        return [
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
            'verified' => (bool) $doctor->is_verified,
            'image' => $doctor->image,
            'latitude' => $facility?->latitude,
            'longitude' => $facility?->longitude,
            'facility' => $facility ? [
                'id' => $facility->id,
                'name' => $facility->name,
                'address' => $facility->address,
                'phone' => $facility->phone,
            ] : null,
        ];
    }
}
