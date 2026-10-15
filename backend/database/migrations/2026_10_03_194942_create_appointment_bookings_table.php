<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('healthcare_provider_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('doctor_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('appointment_date');

            $table->time('appointment_time')->nullable();

            $table->string('appointment_type')->default('in_person');

            $table->text('reason')->nullable();

            $table->string('status')->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_bookings');
    }
};