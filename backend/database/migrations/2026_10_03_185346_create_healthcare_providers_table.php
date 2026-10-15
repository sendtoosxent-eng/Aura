<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('healthcare_providers', function (Blueprint $table) {
            $table->id();

            $table->string('name');

            $table->string('provider_type');
            // hospital, clinic, pharmacy, laboratory

            $table->string('ownership')->nullable();
            // government, private, faith_based

            $table->string('district');

            $table->string('town')->nullable();

            $table->string('address')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->text('description')->nullable();

            $table->decimal('latitude', 10, 7)->nullable();

            $table->decimal('longitude', 10, 7)->nullable();

            $table->decimal('rating', 3, 2)->default(0);

            $table->unsignedInteger('review_count')->default(0);

            $table->boolean('is_verified')->default(false);

            $table->boolean('is_featured')->default(false);

            $table->boolean('open_24_hours')->default(false);

            $table->boolean('emergency_available')->default(false);

            $table->boolean('is_demo')->default(false);

            $table->string('image')->nullable();

            $table->json('services')->nullable();

            $table->timestamps();

            $table->index('provider_type');
            $table->index('district');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('healthcare_providers');
    }
};