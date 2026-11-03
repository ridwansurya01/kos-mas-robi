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
        Schema::table('finances', function (Blueprint $table) {
            $table->boolean('is_sent_to_tenant')->default(false)->after('proof_image');
            $table->timestamp('sent_at')->nullable()->after('is_sent_to_tenant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('finances', function (Blueprint $table) {
            $table->dropColumn(['is_sent_to_tenant', 'sent_at']);
        });
    }
};
