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
        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'wifi_list')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('wifi_list')->nullable()->after('payment_methods');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'wifi_list')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('wifi_list');
            });
        }
    }
};
