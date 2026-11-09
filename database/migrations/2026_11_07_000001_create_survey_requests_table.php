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
        if (!Schema::hasTable('survey_requests')) {
            Schema::create('survey_requests', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('job')->nullable();
                $table->string('branch')->nullable();
                $table->string('room')->nullable();
                $table->date('survey_date')->nullable();
                $table->string('survey_time')->nullable();
                $table->string('survey_method')->nullable();
                $table->text('message')->nullable();
                $table->string('status')->default('pending'); // pending, confirmed, completed, cancelled
                $table->boolean('is_read')->default(false);
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_requests');
    }
};

