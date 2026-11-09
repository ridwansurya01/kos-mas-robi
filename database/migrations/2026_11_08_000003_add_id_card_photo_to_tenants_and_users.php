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
        if (Schema::hasTable('tenants') && !Schema::hasColumn('tenants', 'id_card_photo')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->string('id_card_photo')->nullable()->after('id_card');
            });
        }

        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'id_card_photo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('id_card_photo')->nullable()->after('phone');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tenants') && Schema::hasColumn('tenants', 'id_card_photo')) {
            Schema::table('tenants', function (Blueprint $table) {
                $table->dropColumn('id_card_photo');
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'id_card_photo')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('id_card_photo');
            });
        }
    }
};

