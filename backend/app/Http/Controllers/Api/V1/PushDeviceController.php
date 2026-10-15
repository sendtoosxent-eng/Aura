<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePushDeviceRequest;
use App\Models\PushDevice;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushDeviceController extends Controller
{
    public function update(StorePushDeviceRequest $request): JsonResponse
    {
        $data = $request->validated();
        $normalizedToken = strtolower($data['token']);
        $device = PushDevice::query()->updateOrCreate(
            ['token_hash' => hash('sha256', $normalizedToken)],
            [
                'user_id' => $request->user()->id,
                'token' => $normalizedToken,
                'platform' => 'ios',
                'environment' => $data['environment'],
                'app_bundle_id' => $data['app_bundle_id'],
                'last_seen_at' => now(),
                'disabled_at' => null,
            ],
        );

        return response()->json([
            'message' => 'This device is registered for push notifications.',
            'data' => ['id' => $device->id],
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => ['required', 'string', 'regex:/^[a-fA-F0-9]{64,200}$/'],
        ]);
        $request->user()->pushDevices()
            ->where('token_hash', hash('sha256', strtolower($data['token'])))
            ->update(['disabled_at' => now()]);

        return response()->json(['message' => 'Push notifications are disabled for this device.']);
    }
}
