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
        // 1. Rooms Table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->integer('floor')->default(1);
            $table->string('type');
            $table->decimal('price', 12, 2)->default(0);
            $table->enum('status', ['available', 'occupied', 'due', 'repair'])->default('available');
            $table->json('facilities')->nullable();
            $table->timestamps();
        });

        // 2. Tenants Table
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('id_card')->nullable();
            $table->date('start_date')->nullable();
            $table->integer('due_day')->default(1);
            $table->decimal('rent_amount', 12, 2)->default(0);
            $table->string('emergency_contact')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        // 3. Finances / Transactions Table
        Schema::create('finances', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique();
            $table->foreignId('room_id')->nullable()->constrained('rooms')->nullOnDelete();
            $table->date('date');
            $table->enum('type', ['income', 'expense'])->default('income');
            $table->string('category')->default('Sewa Kamar');
            $table->decimal('amount', 12, 2)->default(0);
            $table->text('description');
            $table->string('payment_method')->default('Transfer BCA');
            $table->timestamps();
        });

        // 4. Maintenance Tickets Table
        Schema::create('maintenance_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_code')->unique();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->string('reporter_name');
            $table->text('issue_description');
            $table->date('report_date');
            $table->string('priority')->default('Normal');
            $table->enum('status', ['pending', 'progress', 'done'])->default('pending');
            $table->timestamps();
        });

        // 5. Kos Settings Table
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('kos_name')->default('Kos Mas Robi - Kampus Terpadu');
            $table->string('owner_name')->default('Mas Robi');
            $table->string('owner_phone')->default('6281234567890');
            $table->text('kos_address');
            $table->string('bank_name')->default('Bank Central Asia (BCA)');
            $table->string('bank_account')->default('8830192831');
            $table->string('bank_holder')->default('Robi Suryanto');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('maintenance_tickets');
        Schema::dropIfExists('finances');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('rooms');
    }
};
