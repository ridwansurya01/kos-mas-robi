<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password_otp', 10)->nullable()->after('password');
            $table->timestamp('password_otp_expires_at')->nullable()->after('password_otp');
            $table->unsignedTinyInteger('password_otp_attempts')->default(0)->after('password_otp_expires_at');
            $table->timestamp('password_otp_sent_at')->nullable()->after('password_otp_attempts');
            $table->timestamp('password_otp_verified_at')->nullable()->after('password_otp_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'password_otp',
                'password_otp_expires_at',
                'password_otp_attempts',
                'password_otp_sent_at',
                'password_otp_verified_at',
            ]);
        });
    }
};
