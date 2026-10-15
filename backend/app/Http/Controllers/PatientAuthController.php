<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PatientAuthController extends Controller
{
    public function status(): JsonResponse
    {
        return response()->json([
            'authenticated' => Auth::check(),

            'user' => Auth::check()
                ? [
                    'id' => Auth::id(),
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]
                : null,
        ]);
    }


    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],
        ]);


        if (
            !Auth::attempt(
                $credentials,
                $request->boolean('remember')
            )
        ) {
            return response()->json([
                'message' =>
                    'The email or password is incorrect.',
            ], 422);
        }


        $request->session()->regenerate();


        return response()->json([
            'message' => 'Welcome back.',

            'user' => [
                'id' => Auth::id(),
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
        ]);
    }


    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:120',
            ],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            'phone' => [
                'required',
                'string',
                'max:30',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],

            'date_of_birth' => [
                'required',
                'date',
                'before:today',
            ],

            'gender' => [
                'nullable',
                'in:male,female,other,prefer_not_to_say',
            ],

            'blood_group' => [
                'nullable',
                'string',
                'max:5',
            ],

            'allergies' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'chronic_conditions' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'current_medications' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:120',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'health_data_consent' => [
                'accepted',
            ],
        ]);


        $user = User::create([
            'name' => $data['name'],

            'email' => $data['email'],

            'phone' => $data['phone'],

            'password' =>
                Hash::make(
                    $data['password']
                ),
        ]);


        PatientProfile::create([
            'user_id' => $user->id,

            'date_of_birth' =>
                $data['date_of_birth'],

            'gender' =>
                $data['gender'] ?? null,

            'blood_group' =>
                $data['blood_group'] ?? null,

            'allergies' =>
                $data['allergies'] ?? null,

            'chronic_conditions' =>
                $data['chronic_conditions']
                ?? null,

            'current_medications' =>
                $data['current_medications']
                ?? null,

            'emergency_contact_name' =>
                $data['emergency_contact_name']
                ?? null,

            'emergency_contact_phone' =>
                $data['emergency_contact_phone']
                ?? null,

            'health_data_consent' => true,
        ]);


        Auth::login($user);

        $request
            ->session()
            ->regenerate();


        return response()->json([
            'message' =>
                'Your AfyaLink account has been created.',

            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }


    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();


        return response()->json([
            'message' => 'Logged out.',
        ]);
    }
}