<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_applications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('application_type');
            // facility / professional

            $table->string('status')
                ->default('draft');
            // draft, submitted, under_review, needs_changes,
            // verified, rejected, suspended

            /*
            |--------------------------------------------------------------------------
            | PROFESSIONAL WORKPLACE
            |--------------------------------------------------------------------------
            */

            $table->foreignId('healthcare_provider_id')
                ->nullable()
                ->constrained('healthcare_providers')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | IDENTITY
            |--------------------------------------------------------------------------
            */

            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();

            $table->date('date_of_birth')->nullable();

            $table->string('sex')->nullable();

            $table->string('nationality')
                ->default('Ugandan');

            $table->string('phone', 30)
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('nin', 14)
                ->nullable();

            $table->string('national_id_front')
                ->nullable();

            $table->string('national_id_back')
                ->nullable();

            $table->string('profile_photo')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | PROFESSIONAL / IHRIS STYLE INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('profession')
                ->nullable();

            $table->string('cadre')
                ->nullable();

            $table->string('specialty')
                ->nullable();

            $table->string('regulatory_body')
                ->nullable();

            $table->string('registration_number')
                ->nullable();

            $table->string('practising_license_number')
                ->nullable();

            $table->date('license_expiry_date')
                ->nullable();

            $table->string('license_status')
                ->nullable();

            $table->string('qualification')
                ->nullable();

            $table->unsignedInteger('years_experience')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | FACILITY INFORMATION
            |--------------------------------------------------------------------------
            */

            $table->string('facility_name')
                ->nullable();

            $table->string('facility_type')
                ->nullable();

            $table->string('ownership')
                ->nullable();

            $table->string('operating_license_number')
                ->nullable();

            $table->date('operating_license_expiry')
                ->nullable();

            $table->string('ursb_registration_number')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | LOCATION
            |--------------------------------------------------------------------------
            */

            $table->string('district')
                ->nullable();

            $table->string('town')
                ->nullable();

            $table->string('address')
                ->nullable();

            $table->decimal(
                'latitude',
                10,
                7
            )->nullable();

            $table->decimal(
                'longitude',
                10,
                7
            )->nullable();

            /*
            |--------------------------------------------------------------------------
            | VERIFICATION
            |--------------------------------------------------------------------------
            */

            $table->boolean('phone_verified')
                ->default(false);

            $table->boolean('email_verified')
                ->default(false);

            $table->boolean('identity_verified')
                ->default(false);

            $table->boolean('regulator_verified')
                ->default(false);

            $table->timestamp('submitted_at')
                ->nullable();

            $table->timestamp('verified_at')
                ->nullable();

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->text('verification_notes')
                ->nullable();

            $table->timestamps();

            $table->index('application_type');

            $table->index('status');

            $table->index('registration_number');

            $table->index('practising_license_number');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'registration_applications'
        );
    }
};