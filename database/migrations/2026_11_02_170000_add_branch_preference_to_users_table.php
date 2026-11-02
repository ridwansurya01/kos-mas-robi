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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'branch_preference')) {
                $table->string('branch_preference')->nullable()->default('Lokasi 1')->after('room_id');
            }
            if (!Schema::hasColumn('users', 'room_type_preference')) {
                $table->string('room_type_preference')->nullable()->after('branch_preference');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'branch_preference')) {
                $table->dropColumn('branch_preference');
            }
            if (Schema::hasColumn('users', 'room_type_preference')) {
                $table->dropColumn('room_type_preference');
            }
        });
    }
};

