<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('gender');
            $table->string('cover_photo')->nullable()->after('profile_photo');
            $table->unsignedSmallInteger('systolic')->nullable()->after('blood_group');
            $table->unsignedSmallInteger('diastolic')->nullable()->after('systolic');
            $table->unsignedSmallInteger('heart_rate')->nullable()->after('diastolic');
            $table->decimal('height_cm', 5, 2)->nullable()->after('heart_rate');
            $table->decimal('weight_kg', 5, 2)->nullable()->after('height_cm');
            $table->string('emergency_contact_relationship')->nullable()->after('emergency_contact_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patient_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'profile_photo',
                'cover_photo',
                'systolic',
                'diastolic',
                'heart_rate',
                'height_cm',
                'weight_kg',
                'emergency_contact_relationship',
            ]);
        });
    }
};
