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
        Schema::table('rooms', function (Blueprint $table) {
            $table->text('custom_interior_request')->nullable();
            $table->decimal('custom_interior_budget', 12, 2)->default(0);
            $table->string('custom_interior_status')->default('none'); // none, pending, approved, in_progress, completed, rejected
            $table->text('custom_interior_admin_notes')->nullable();
            $table->foreignId('booked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('booked_by_name')->nullable();
            $table->string('booked_by_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['booked_by_user_id']);
            $table->dropColumn([
                'custom_interior_request',
                'custom_interior_budget',
                'custom_interior_status',
                'custom_interior_admin_notes',
                'booked_by_user_id',
                'booked_by_name',
                'booked_by_phone',
            ]);
        });
    }
};
