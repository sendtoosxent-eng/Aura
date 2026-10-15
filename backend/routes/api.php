<?php

use App\Http\Controllers\Api\V1\AppointmentController as ApiAppointmentController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\DiscoveryController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\MobileDataController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\PatientProfileController;
use App\Http\Controllers\Api\V1\PatientRegistrationController;
use App\Http\Controllers\Api\V1\ProviderAvailabilityController;
use App\Http\Controllers\Api\V1\ProviderRegistrationController as ApiProviderRegistrationController;
use App\Http\Controllers\Api\V1\PushDeviceController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/health', fn (): JsonResponse => response()->json([
        'status' => 'ok',
        'service' => 'AfyaLink API',
        'version' => 'v1',
    ]));

    Route::prefix('auth')->middleware('throttle:10,1')->group(function (): void {
        Route::post('/lookup', [AuthController::class, 'lookup']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/login/verify', [AuthController::class, 'verifyLogin']);
        Route::post('/login/resend', [AuthController::class, 'resendLoginCode']);
    });

    Route::post('/patient-registration', [PatientRegistrationController::class, 'store'])
        ->middleware('throttle:5,1');

    Route::get('/providers', [DiscoveryController::class, 'providers']);
    Route::get('/providers/{provider}', [DiscoveryController::class, 'provider'])->whereNumber('provider');
    Route::get('/providers/{provider}/availability', [ProviderAvailabilityController::class, 'show'])
        ->whereNumber('provider');
    Route::get('/doctors', [DiscoveryController::class, 'doctors']);
    Route::get('/doctors/{doctor}', [DiscoveryController::class, 'doctor'])->whereNumber('doctor');

    Route::prefix('provider-registration')->group(function (): void {
        Route::get('/facilities', [ApiProviderRegistrationController::class, 'facilities']);
        Route::post('/identity', [ApiProviderRegistrationController::class, 'createIdentity'])
            ->middleware('throttle:5,1');
    });

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::patch('/auth/two-factor', [AuthController::class, 'updateTwoFactor']);

        Route::put('/push/devices', [PushDeviceController::class, 'update']);
        Route::delete('/push/devices', [PushDeviceController::class, 'destroy']);

        Route::post('/profile', [PatientProfileController::class, 'update']);
        Route::get('/profile/photos/{type}', [PatientProfileController::class, 'photo'])
            ->whereIn('type', ['profile', 'cover']);

        Route::get('/mobile-data', MobileDataController::class);
        Route::post('/message-threads', [MessageController::class, 'start']);
        Route::post('/message-threads/{thread}/messages', [MessageController::class, 'store']);
        Route::post('/message-threads/{thread}/read', [MessageController::class, 'markRead']);
        Route::patch('/notifications/{notification}/read', [NotificationController::class, 'update']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead']);

        Route::get('/provider-availability', [ProviderAvailabilityController::class, 'index']);
        Route::post('/provider-availability', [ProviderAvailabilityController::class, 'store']);
        Route::delete('/provider-availability/{unavailableDate}', [ProviderAvailabilityController::class, 'destroy'])
            ->whereNumber('unavailableDate');

        Route::prefix('patient-registration')->group(function (): void {
            Route::post('/phone/send', [PatientRegistrationController::class, 'sendPhoneOtp'])
                ->middleware('throttle:3,1');
            Route::post('/phone/verify', [PatientRegistrationController::class, 'verifyPhoneOtp'])
                ->middleware('throttle:10,1');
        });

        Route::prefix('provider-registration')->group(function (): void {
            Route::get('/status', [ApiProviderRegistrationController::class, 'status']);
            Route::post('/email/resend', [ApiProviderRegistrationController::class, 'resendEmail'])
                ->middleware('throttle:6,1');
            Route::post('/phone/send', [ApiProviderRegistrationController::class, 'sendPhoneOtp'])
                ->middleware('throttle:3,1');
            Route::post('/phone/verify', [ApiProviderRegistrationController::class, 'verifyPhoneOtp'])
                ->middleware('throttle:10,1');
            Route::post('/application', [RegistrationController::class, 'submitApplication'])
                ->middleware(['verified', 'throttle:5,1']);
        });

        Route::get('/appointments', [ApiAppointmentController::class, 'index']);
        Route::post('/appointments', [ApiAppointmentController::class, 'store']);
        Route::patch('/appointments/{appointment}', [ApiAppointmentController::class, 'update']);
        Route::post('/appointments/{appointment}/cancel', [ApiAppointmentController::class, 'cancel']);
    });
});
