<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('phone_verifications', function (Blueprint $table) {

            $table->id();

            $table->string('phone');

            $table->string('otp_hash');

            $table->unsignedTinyInteger('attempts')
                ->default(0);

            $table->timestamp('expires_at');

            $table->timestamp('verified_at')
                ->nullable();

            $table->timestamp('last_sent_at')
                ->nullable();

            $table->string('ip_address', 45)
                ->nullable();

            $table->timestamps();

            $table->index('phone');
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('phone_verifications');
    }
};