<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();

            $table->foreignId('healthcare_provider_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('name');

            $table->string('specialty');

            $table->string('district');

            $table->string('town')->nullable();

            $table->string('phone')->nullable();

            $table->string('email')->nullable();

            $table->text('bio')->nullable();

            $table->decimal('rating', 3, 2)->default(0);

            $table->unsignedInteger('review_count')->default(0);

            $table->boolean('is_verified')->default(false);

            $table->boolean('is_featured')->default(false);

            $table->boolean('is_demo')->default(false);

            $table->string('image')->nullable();

            $table->timestamps();

            $table->index('specialty');
            $table->index('district');
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};