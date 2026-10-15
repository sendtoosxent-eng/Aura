<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('phone', 30)
                ->nullable()
                ->unique()
                ->after('email');

            $table->timestamp('phone_verified_at')
                ->nullable()
                ->after('email_verified_at');

            $table->string('nin', 14)
                ->nullable()
                ->unique()
                ->after('phone_verified_at');

            $table->string('account_type', 30)
                ->default('patient')
                ->after('nin');

            $table->string('registration_status', 30)
                ->default('draft')
                ->after('account_type');
        });
    }


    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn([
                'phone',
                'phone_verified_at',
                'nin',
                'account_type',
                'registration_status',
            ]);
        });
    }
};