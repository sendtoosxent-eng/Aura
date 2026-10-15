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
        Schema::create('provider_unavailable_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('healthcare_provider_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('unavailable_date');
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(['healthcare_provider_id', 'unavailable_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_unavailable_dates');
    }
};
