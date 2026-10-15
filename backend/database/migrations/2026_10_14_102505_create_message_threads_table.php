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
        Schema::create('message_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('provider_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('healthcare_provider_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->boolean('is_automated')->default(false);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();

            $table->index(['patient_user_id', 'last_activity_at']);
            $table->index(['provider_user_id', 'last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_threads');
    }
};
