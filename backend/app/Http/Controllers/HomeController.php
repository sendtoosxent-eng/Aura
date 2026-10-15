<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\HealthcareProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $topProviders = HealthcareProvider::query()
            ->where('is_verified', true)
            ->whereIn('provider_type', ['hospital', 'clinic'])
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->limit(6)
            ->get();

        $topDoctors = Doctor::query()
            ->with('healthcareProvider')
            ->where('is_verified', true)
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->limit(4)
            ->get();

        $mapProviders = HealthcareProvider::query()
            ->where('is_verified', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->orderBy('district')
            ->get();

        $districts = HealthcareProvider::query()
            ->select('district')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        return view('welcome', compact(
            'topProviders',
            'topDoctors',
            'mapProviders',
            'districts'
        ));
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:200'],
            'category' => ['nullable', 'in:hospital,clinic,pharmacy,laboratory,doctor,emergency'],
        ]);

        $query = trim((string) $request->query('q', ''));
        $category = $request->query('category');

        if (! $category && mb_strlen($query) < 2) {
            return response()->json([
                'providers' => [],
                'doctors' => [],
            ]);
        }

        $providers = HealthcareProvider::query()
            ->where('is_verified', true)
            ->where(function ($builder) use ($query) {

                $builder
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('provider_type', 'like', "%{$query}%")
                    ->orWhere('ownership', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('town', 'like', "%{$query}%")
                    ->orWhere('address', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('services', 'like', "%{$query}%");

            })
            ->when($category && ! in_array($category, ['doctor', 'emergency'], true), fn ($builder) => $builder->where('provider_type', $category))
            ->when($category === 'emergency', fn ($builder) => $builder->where('emergency_available', true))
            ->when($category === 'doctor', fn ($builder) => $builder->whereRaw('1 = 0'))
            ->orderByDesc('rating')
            ->when(! $category, fn ($builder) => $builder->limit(8))
            ->get([
                'id',
                'name',
                'provider_type',
                'ownership',
                'district',
                'town',
                'address',
                'description',
                'services',
                'phone',
                'rating',
                'review_count',
                'image',
                'is_verified',
                'open_24_hours',
                'emergency_available',
            ]);

        $doctors = Doctor::query()
            ->with('healthcareProvider:id,name')
            ->where('is_verified', true)
            ->where(function ($builder) use ($query) {

                $builder
                    ->where('name', 'like', "%{$query}%")
                    ->orWhere('specialty', 'like', "%{$query}%")
                    ->orWhere('district', 'like', "%{$query}%")
                    ->orWhere('town', 'like', "%{$query}%");

            })
            ->when($category && $category !== 'doctor', fn ($builder) => $builder->whereRaw('1 = 0'))
            ->orderByDesc('rating')
            ->when(! $category, fn ($builder) => $builder->limit(6))
            ->get();

        return response()->json([
            'providers' => $providers,
            'doctors' => $doctors,
        ]);
    }
}
