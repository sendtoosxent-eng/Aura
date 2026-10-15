<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->date('date_of_birth')->nullable();

            $table->string('gender')->nullable();

            $table->string('blood_group')->nullable();

            $table->text('allergies')->nullable();

            $table->text('chronic_conditions')->nullable();

            $table->text('current_medications')->nullable();

            $table->string('emergency_contact_name')->nullable();

            $table->string('emergency_contact_phone')->nullable();

            $table->boolean('health_data_consent')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patient_profiles');
    }
};