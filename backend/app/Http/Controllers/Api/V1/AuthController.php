<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\LoginVerification;
use App\Models\User;
use App\Services\LoginSecurityService;
use App\Services\MobileProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuthController extends Controller
{
    public function profilePhoto(User $user): StreamedResponse
    {
        $path = $user->account_type === 'patient'
            ? $user->patientProfile?->profile_photo
            : $user->registrationApplications()->latest('id')->value('profile_photo');

        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->response($path, headers: [
            'Cache-Control' => 'private, max-age=300',
        ]);
    }

    public function lookup(
        Request $request,
        LoginSecurityService $security,
        MobileProfileService $profiles,
    ): JsonResponse {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'role' => ['required', Rule::in(['patient', 'provider'])],
            'device_id' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()->where('phone', $data['phone'])->first();

        if (! $user || ! $this->roleMatches($user, $data['role'])) {
            return response()->json(['message' => 'We could not find an account matching those details.'], 404);
        }

        return response()->json([
            'data' => $profiles->safe($user) + [
                'requires_security_otp' => $user->two_factor_enabled && ! $security->isTrusted(
                    $user,
                    $request->ip() ?? 'unknown',
                    $data['device_id'],
                ),
            ],
        ]);
    }

    public function login(
        Request $request,
        LoginSecurityService $security,
        MobileProfileService $profiles,
    ): JsonResponse {
        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
            'role' => ['required', Rule::in(['patient', 'provider'])],
            'device_id' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::query()->where('phone', $data['phone'])->first();

        if (! $user || ! $this->roleMatches($user, $data['role']) || ! Hash::check($data['password'], $user->password)) {
            return response()->json(['message' => 'The phone number or password is incorrect.'], 422);
        }

        $ipAddress = $request->ip() ?? 'unknown';

        if ($user->two_factor_enabled && ! $security->isTrusted($user, $ipAddress, $data['device_id'])) {
            $result = $security->createChallenge(
                $user,
                $ipAddress,
                $data['device_id'],
                $request->userAgent(),
            );

            return response()->json(array_filter([
                'message' => 'Enter the security code sent to your phone.',
                'requires_security_otp' => true,
                'challenge_id' => $result['challenge']->id,
                'expires_at' => $result['challenge']->expires_at->toIso8601String(),
                'debug_code' => $result['debug_code'],
            ], fn ($value) => $value !== null), 202);
        }

        $security->touchTrustedLogin($user, $ipAddress, $data['device_id'], $request->userAgent());

        return $this->tokenResponse($user, $data['device_name'], $profiles);
    }

    public function verifyLogin(
        Request $request,
        LoginSecurityService $security,
        MobileProfileService $profiles,
    ): JsonResponse {
        $data = $request->validate([
            'challenge_id' => ['required', 'integer', 'exists:login_verifications,id'],
            'otp' => ['required', 'digits:6'],
            'device_id' => ['required', 'string', 'max:255'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $challenge = LoginVerification::query()->findOrFail($data['challenge_id']);
        $user = $security->verifyChallenge(
            $challenge,
            $data['otp'],
            $request->ip() ?? 'unknown',
            $data['device_id'],
            $request->userAgent(),
        );

        return $this->tokenResponse($user, $data['device_name'], $profiles);
    }

    public function resendLoginCode(Request $request, LoginSecurityService $security): JsonResponse
    {
        $data = $request->validate([
            'challenge_id' => ['required', 'integer', 'exists:login_verifications,id'],
            'device_id' => ['required', 'string', 'max:255'],
        ]);

        $challenge = LoginVerification::query()->findOrFail($data['challenge_id']);
        $result = $security->resendChallenge(
            $challenge,
            $request->ip() ?? 'unknown',
            $data['device_id'],
            $request->userAgent(),
        );

        return response()->json(array_filter([
            'message' => 'A new login code has been sent to your phone.',
            'challenge_id' => $result['challenge']->id,
            'expires_at' => $result['challenge']->expires_at->toIso8601String(),
            'debug_code' => $result['debug_code'],
        ], fn ($value) => $value !== null));
    }

    public function me(Request $request, MobileProfileService $profiles): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'data' => $profiles->authenticated($user),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Logged out.']);
    }

    public function updateTwoFactor(Request $request, MobileProfileService $profiles): JsonResponse
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
        ]);
        $user = $request->user();
        $user->update(['two_factor_enabled' => $data['enabled']]);

        if (! $user->two_factor_enabled) {
            $user->loginVerifications()->whereNull('verified_at')->delete();
        }

        return response()->json([
            'message' => $user->two_factor_enabled
                ? 'Two-factor authentication is on.'
                : 'Two-factor authentication is off.',
            'data' => $profiles->authenticated($user->fresh()),
        ]);
    }

    private function tokenResponse(
        User $user,
        string $deviceName,
        MobileProfileService $profiles,
    ): JsonResponse {
        $token = $user->createToken($deviceName, ['mobile'])->plainTextToken;

        return response()->json([
            'message' => 'Welcome back to AfyaLink.',
            'requires_security_otp' => false,
            'token_type' => 'Bearer',
            'access_token' => $token,
            'data' => $profiles->authenticated($user),
        ]);
    }

    private function roleMatches(User $user, string $role): bool
    {
        return $role === 'provider'
            ? in_array($user->account_type, ['facility', 'professional'], true)
            : ! in_array($user->account_type, ['facility', 'professional'], true);
    }
}
