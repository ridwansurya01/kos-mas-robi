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
        if (Schema::hasTable('rooms') && !Schema::hasColumn('rooms', 'location')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->string('location')->default('Lokasi 1')->after('type');
            });
        }

        if (Schema::hasTable('settings') && !Schema::hasColumn('settings', 'branches')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->json('branches')->nullable()->after('location_highlights');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('rooms') && Schema::hasColumn('rooms', 'location')) {
            Schema::table('rooms', function (Blueprint $table) {
                $table->dropColumn('location');
            });
        }

        if (Schema::hasTable('settings') && Schema::hasColumn('settings', 'branches')) {
            Schema::table('settings', function (Blueprint $table) {
                $table->dropColumn('branches');
            });
        }
    }
};
