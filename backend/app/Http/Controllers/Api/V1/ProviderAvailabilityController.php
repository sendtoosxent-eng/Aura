<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProviderUnavailableDateRequest;
use App\Models\HealthcareProvider;
use App\Models\ProviderUnavailableDate;
use App\Services\ProviderAccountResolver;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ProviderAvailabilityController extends Controller
{
    public function show(Request $request, HealthcareProvider $provider): JsonResponse
    {
        $data = $request->validate(['month' => ['required', 'date_format:Y-m']]);
        $month = CarbonImmutable::createFromFormat('Y-m-d', $data['month'].'-01')->startOfMonth();

        return response()->json([
            'data' => [
                'provider_id' => $provider->id,
                'month' => $month->format('Y-m'),
                'unavailable_dates' => $provider->unavailableDates()
                    ->whereBetween('unavailable_date', [$month, $month->endOfMonth()])
                    ->orderBy('unavailable_date')
                    ->get()
                    ->map(fn (ProviderUnavailableDate $date): array => $this->payload($date)),
            ],
        ]);
    }

    public function index(Request $request, ProviderAccountResolver $resolver): JsonResponse
    {
        $provider = $this->resolveProvider($request, $resolver);

        return response()->json([
            'data' => $provider->unavailableDates()
                ->whereDate('unavailable_date', '>=', today())
                ->orderBy('unavailable_date')
                ->get()
                ->map(fn (ProviderUnavailableDate $date): array => $this->payload($date)),
        ]);
    }

    public function store(
        StoreProviderUnavailableDateRequest $request,
        ProviderAccountResolver $resolver,
    ): JsonResponse {
        $provider = $this->resolveProvider($request, $resolver);
        $data = $request->validated();
        $unavailableDate = $provider->unavailableDates()->updateOrCreate(
            ['unavailable_date' => $data['date']],
            ['created_by_user_id' => $request->user()->id, 'reason' => $data['reason'] ?? null],
        );

        return response()->json([
            'message' => 'This date is now unavailable for appointments.',
            'data' => $this->payload($unavailableDate),
        ], $unavailableDate->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(
        Request $request,
        int $unavailableDate,
        ProviderAccountResolver $resolver,
    ): JsonResponse {
        $provider = $this->resolveProvider($request, $resolver);
        $date = $provider->unavailableDates()->findOrFail($unavailableDate);
        $date->delete();

        return response()->json(['message' => 'This date is available again.']);
    }

    private function resolveProvider(Request $request, ProviderAccountResolver $resolver): HealthcareProvider
    {
        $provider = $resolver->healthcareProvider($request->user());
        if (! $provider) {
            throw ValidationException::withMessages([
                'provider' => 'This account is not linked to a healthcare provider.',
            ]);
        }

        return $provider;
    }

    /** @return array{id: int, date: string, reason: ?string} */
    private function payload(ProviderUnavailableDate $date): array
    {
        return [
            'id' => $date->id,
            'date' => $date->unavailable_date->toDateString(),
            'reason' => $date->reason,
        ];
    }
}
