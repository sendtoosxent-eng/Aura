<?php

namespace App\Http\Controllers;

use App\Models\HealthcareProvider;
use App\Models\RegistrationApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProviderLoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW LOGIN PAGE
    |--------------------------------------------------------------------------
    */

    public function show()
    {
        return view('auth.provider-login');
    }


    /*
    |--------------------------------------------------------------------------
    | FIND ACCOUNT BY AFYALINK REGISTRATION NUMBER
    |--------------------------------------------------------------------------
    */

    public function findAccount(Request $request)
    {
        $data = $request->validate([
            'registration_number' => [
                'required',
                'string',
                'max:50',
            ],
        ]);


        $registrationNumber =
            strtoupper(
                trim(
                    $data['registration_number']
                )
            );


        $user = User::query()
            ->where(
                'registration_number',
                $registrationNumber
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | ACCOUNT NOT FOUND
        |--------------------------------------------------------------------------
        */

        if (!$user) {

            return response()->json([
                'message' =>
                    'We could not find an AfyaLink account with that registration number.',
            ], 404);

        }


        /*
        |--------------------------------------------------------------------------
        | ONLY FACILITIES + PROFESSIONALS USE THIS LOGIN
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $user->account_type,
                [
                    'facility',
                    'professional',
                ],
                true
            )
        ) {

            return response()->json([
                'message' =>
                    'This account cannot use the AfyaLink provider login.',
            ], 403);

        }


        /*
        |--------------------------------------------------------------------------
        | GET REGISTRATION APPLICATION
        |--------------------------------------------------------------------------
        */

        $application =
            RegistrationApplication::query()
                ->where(
                    'user_id',
                    $user->id
                )
                ->latest('id')
                ->first();


        /*
        |--------------------------------------------------------------------------
        | PERSON NAME
        |--------------------------------------------------------------------------
        */

        $firstName =
            $application?->first_name
            ?: 'AfyaLink';


        $middleName =
            $application?->middle_name;


        $lastName =
            $application?->last_name
            ?: 'Member';


        $personName =
            collect([
                $firstName,
                $middleName,
                $lastName,
            ])
                ->filter()
                ->implode(' ');


        /*
        |--------------------------------------------------------------------------
        | DEFAULT PROFILE VALUES
        |--------------------------------------------------------------------------
        */

        $displayName =
            $personName;


        $facilityName =
            null;


        $accountLabel =
            $user->account_type === 'facility'
                ? 'Healthcare Facility'
                : 'Health Professional';


        /*
        |--------------------------------------------------------------------------
        | PROFESSIONAL ACCOUNT
        |--------------------------------------------------------------------------
        */

        if (
            $user->account_type ===
            'professional'
        ) {

            if (
                $application?->healthcare_provider_id
            ) {

                $facility =
                    HealthcareProvider::query()
                        ->find(
                            $application
                                ->healthcare_provider_id
                        );


                $facilityName =
                    $facility?->name;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | FACILITY ACCOUNT
        |--------------------------------------------------------------------------
        */

        if (
            $user->account_type ===
            'facility'
        ) {

            /*
            | The facility name normally comes from
            | registration_applications.facility_name.
            */

            $facilityName =
                $application?->facility_name;


            /*
            | Show facility name as the main account name
            | if it exists.
            */

            if ($facilityName) {

                $displayName =
                    $facilityName;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE PHOTO
        |--------------------------------------------------------------------------
        |
        | For now we return whether a photo exists.
        |
        | The actual registration photo is stored privately,
        | so we should NOT expose its raw storage path publicly.
        |
        */

        $hasProfilePhoto =
            !empty(
                $application?->profile_photo
            );


        /*
        |--------------------------------------------------------------------------
        | RETURN SAFE ACCOUNT PREVIEW
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'account' => [

                'registration_number' =>
                    $user->registration_number,

                'name' =>
                    $displayName,

                'person_name' =>
                    $personName,

                'initials' =>
                    $this->initials(
                        $displayName
                    ),

                'account_type' =>
                    $user->account_type,

                'account_label' =>
                    $accountLabel,

                'facility_name' =>
                    $facilityName,

                'status' =>
                    $user->registration_status
                    ?: 'draft',

                'has_profile_photo' =>
                    $hasProfilePhoto,

            ],

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATE ACCOUNT
    |--------------------------------------------------------------------------
    */

    public function authenticate(Request $request)
    {
        $data = $request->validate([

            'registration_number' => [
                'required',
                'string',
                'max:50',
            ],

            'password' => [
                'required',
                'string',
            ],

            'remember' => [
                'nullable',
                'boolean',
            ],

        ]);


        $registrationNumber =
            strtoupper(
                trim(
                    $data['registration_number']
                )
            );


        /*
        |--------------------------------------------------------------------------
        | FIND USER
        |--------------------------------------------------------------------------
        */

        $user = User::query()
            ->where(
                'registration_number',
                $registrationNumber
            )
            ->first();


        /*
        |--------------------------------------------------------------------------
        | INVALID LOGIN
        |--------------------------------------------------------------------------
        */

        if (
            !$user
            ||
            !Hash::check(
                $data['password'],
                $user->password
            )
        ) {

            return response()->json([
                'message' =>
                    'The registration number or password is incorrect.',
            ], 422);

        }


        /*
        |--------------------------------------------------------------------------
        | MAKE SURE ACCOUNT IS ALLOWED
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $user->account_type,
                [
                    'facility',
                    'professional',
                ],
                true
            )
        ) {

            return response()->json([
                'message' =>
                    'This account cannot use the AfyaLink provider login.',
            ], 403);

        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN
        |--------------------------------------------------------------------------
        */

        Auth::login(
            $user,
            $request->boolean(
                'remember'
            )
        );


        /*
        |--------------------------------------------------------------------------
        | REGENERATE SESSION
        |--------------------------------------------------------------------------
        */

        $request
            ->session()
            ->regenerate();


        /*
        |--------------------------------------------------------------------------
        | DASHBOARD REDIRECT
        |--------------------------------------------------------------------------
        */

        $redirect =
            $user->account_type ===
            'facility'

                ? route('provider.dashboard')

                : route('professional.dashboard');


        return response()->json([

            'message' =>
                'Welcome back to AfyaLink.',

            'account_type' =>
                $user->account_type,

            'redirect' =>
                $redirect,

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE INITIALS
    |--------------------------------------------------------------------------
    */

    private function initials(
        string $name
    ): string {

        $words =
            preg_split(
                '/\s+/',
                trim($name)
            );


        $words =
            array_values(
                array_filter(
                    $words
                )
            );


        if (empty($words)) {

            return 'AL';

        }


        if (
            count($words) === 1
        ) {

            return strtoupper(
                mb_substr(
                    $words[0],
                    0,
                    2
                )
            );

        }


        return strtoupper(

            mb_substr(
                $words[0],
                0,
                1
            )

            .

            mb_substr(
                $words[
                    count($words) - 1
                ],
                0,
                1
            )

        );
    }
}