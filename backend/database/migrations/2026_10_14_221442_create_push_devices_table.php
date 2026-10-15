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
        Schema::create('push_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->char('token_hash', 64)->unique();
            $table->text('token');
            $table->string('platform', 20)->default('ios');
            $table->string('environment', 20)->default('development');
            $table->string('app_bundle_id')->default('ug.afyalink.ios');
            $table->timestamp('last_seen_at');
            $table->timestamp('disabled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'disabled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('push_devices');
    }
};
