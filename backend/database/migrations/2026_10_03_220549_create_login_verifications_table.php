<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('login_verifications', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('token_hash');

            $table->string('ip_address', 45);

            $table->string('device_hash');

            $table->string('user_agent', 500)
                ->nullable();

            $table->timestamp('expires_at');

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamps();

            $table->index('user_id');

            $table->index([
                'user_id',
                'ip_address',
                'device_hash',
            ]);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('login_verifications');
    }
};