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
        if (!Schema::hasColumn('settings', 'payment_methods')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('payment_methods')->nullable()->after('bank_holder');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('settings', 'payment_methods')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('payment_methods');
            });
        }
    }
};

