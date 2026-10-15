<?php

namespace App\Http\Controllers;

use App\Models\RegistrationApplication;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SMART DASHBOARD REDIRECT
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $user = $request->user();


        if (!$user) {

            return redirect()
                ->route('provider.login');

        }


        return match ($user->account_type) {

            'facility' =>
                redirect()
                    ->route('provider.dashboard'),

            'professional' =>
                redirect()
                    ->route('professional.dashboard'),

            'patient' =>
                redirect()
                    ->route('patient.dashboard'),

            default =>
                redirect()
                    ->route('home'),

        };
    }


    /*
    |--------------------------------------------------------------------------
    | FACILITY DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function facility(Request $request)
    {
        $user = $request->user();


        abort_unless(
            $user->account_type === 'facility',
            403
        );


        $application =
            RegistrationApplication::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->latest('id')
                ->first();


        return view(
            'dashboards.facility',
            [
                'user' =>
                    $user,

                'application' =>
                    $application,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PROFESSIONAL DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function professional(Request $request)
    {
        $user = $request->user();


        abort_unless(
            $user->account_type === 'professional',
            403
        );


        $application =
            RegistrationApplication::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->latest('id')
                ->first();


        return view(
            'dashboards.professional',
            [
                'user' =>
                    $user,

                'application' =>
                    $application,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | PATIENT DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function patient(Request $request)
    {
        $user = $request->user();


        abort_unless(
            $user->account_type === 'patient',
            403
        );


        return view(
            'dashboards.patient',
            [
                'user' =>
                    $user,
            ]
        );
    }
}