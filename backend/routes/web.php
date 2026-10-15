<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthcareProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientAuthController;
use App\Http\Controllers\ProviderLoginController;
use App\Http\Controllers\RegistrationController;
use App\Models\RegistrationApplication;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get(
    '/',
    [HomeController::class, 'index']
)->name('home');

Route::get(
    '/health-search',
    [HomeController::class, 'search']
)->name('health.search');

/*
|--------------------------------------------------------------------------
| HEALTHCARE PROFILES
|--------------------------------------------------------------------------
*/

Route::get(
    '/providers/{provider}/profile',
    [HealthcareProfileController::class, 'provider']
)->name('providers.profile');

Route::get(
    '/doctors/{doctor}/profile',
    [HealthcareProfileController::class, 'doctor']
)->name('doctors.profile');

/*
|--------------------------------------------------------------------------
| PATIENT AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::get(
    '/patient-auth/status',
    [PatientAuthController::class, 'status']
)->name('patient.auth.status');

Route::post(
    '/patient-auth/login',
    [PatientAuthController::class, 'login']
)->name('patient.auth.login');

Route::post(
    '/patient-auth/register',
    [PatientAuthController::class, 'register']
)->name('patient.auth.register');

Route::post(
    '/patient-auth/logout',
    [PatientAuthController::class, 'logout']
)->name('patient.auth.logout');

/*
|--------------------------------------------------------------------------
| APPOINTMENTS
|--------------------------------------------------------------------------
*/

Route::post(
    '/appointments',
    [AppointmentController::class, 'store']
)
    ->middleware('auth')
    ->name('appointments.store');

/*
|--------------------------------------------------------------------------
| AFYALINK FACILITY / PROFESSIONAL REGISTRATION
|--------------------------------------------------------------------------
*/

Route::get(
    '/join-afyalink',
    [RegistrationController::class, 'index']
)->name('registration.start');

Route::get(
    '/join-afyalink/facilities',
    [RegistrationController::class, 'facilities']
)->name('registration.facilities');

Route::post(
    '/join-afyalink/identity',
    [RegistrationController::class, 'createIdentity']
)
    ->middleware('guest')
    ->middleware('throttle:5,1')
    ->name('registration.identity');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED REGISTRATION ACTIONS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post(
        '/join-afyalink/identity/update',
        [RegistrationController::class, 'updateIdentity']
    )
        ->middleware('throttle:10,1')
        ->name('registration.identity.update');

    /*
    |--------------------------------------------------------------------------
    | REGISTRATION EMAIL STATUS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/join-afyalink/email/status',
        [RegistrationController::class, 'emailStatus']
    )->name('registration.email.status');

    Route::post(
        '/join-afyalink/application',
        [RegistrationController::class, 'submitApplication']
    )
        ->middleware(['verified', 'throttle:5,1'])
        ->name('registration.application.submit');

    /*
    |--------------------------------------------------------------------------
    | EMAIL VERIFICATION NOTICE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/email/verify',
        function () {

            return redirect()
                ->route('registration.start');

        }
    )->name('verification.notice');

    /*
    |--------------------------------------------------------------------------
    | RESEND EMAIL VERIFICATION
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/email/verification-notification',
        function (Request $request) {

            $user = $request->user();

            if ($user->hasVerifiedEmail()) {

                return response()->json([
                    'message' => 'Your email address is already verified.',

                    'verified' => true,
                ]);
            }

            $user
                ->sendEmailVerificationNotification();

            return response()->json([
                'message' => 'A new verification email has been sent.',

                'verified' => false,
            ]);

        }
    )
        ->middleware('throttle:6,1')
        ->name('verification.send');

});

/*
|--------------------------------------------------------------------------
| EMAIL VERIFICATION LINK
|--------------------------------------------------------------------------
*/

Route::get(
    '/email/verify/{id}/{hash}',
    function (Request $request, string $id, string $hash) {
        $user = User::query()->findOrFail($id);

        abort_unless(
            hash_equals((string) $hash, sha1($user->getEmailForVerification())),
            403
        );

        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        RegistrationApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'draft')
            ->latest('id')
            ->first()
            ?->update(['email_verified' => true]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('registration.start')
            ->with(
                'status',
                'email-verified'
            );

    }
)
    ->middleware([
        'signed',
        'throttle:6,1',
    ])
    ->name('verification.verify');

/*
|--------------------------------------------------------------------------
| FACILITY / PROFESSIONAL LOGIN
|--------------------------------------------------------------------------
|
| Do NOT place these routes inside guest middleware for now.
| Registration may already have authenticated the user.
|
*/

Route::get(
    '/provider/sign-in',
    [ProviderLoginController::class, 'show']
)->name('provider.login');

Route::post(
    '/provider/sign-in/find-account',
    [ProviderLoginController::class, 'findAccount']
)
    ->middleware('throttle:10,1')
    ->name('provider.login.find');

Route::post(
    '/provider/sign-in/authenticate',
    [ProviderLoginController::class, 'authenticate']
)
    ->middleware('throttle:5,1')
    ->name('provider.login.authenticate');

/*
|--------------------------------------------------------------------------
| DASHBOARDS
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | SMART DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | FACILITY DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/provider/dashboard',
        [DashboardController::class, 'facility']
    )->name('provider.dashboard');

    /*
    |--------------------------------------------------------------------------
    | PROFESSIONAL DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/professional/dashboard',
        [DashboardController::class, 'professional']
    )->name('professional.dashboard');

    /*
    |--------------------------------------------------------------------------
    | PATIENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/patient/dashboard',
        [DashboardController::class, 'patient']
    )->name('patient.dashboard');

});
