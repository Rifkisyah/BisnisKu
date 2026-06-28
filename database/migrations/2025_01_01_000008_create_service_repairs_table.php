<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_repairs', function (Blueprint $table) {
            $table->string('repair_code')->primary();
            $table->foreignId('technician_id')->constrained('users')->onDelete('restrict');
            $table->string('customer_name');
            $table->string('customer_contact')->nullable();
            $table->decimal('service_fee', 15, 2)->default(0);
            $table->decimal('component_cost', 15, 2)->default(0);
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->enum('payment_method', ['cash', 'transfer', 'qris'])->default('cash');
            $table->decimal('down_payment', 15, 2)->default(0);
            $table->enum('status', ['received', 'in_progress', 'waiting_for_parts', 'completed', 'canceled', 'picked_up'])->default('received');
            $table->dateTime('start_date');
            $table->dateTime('completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_repairs');
    }
};
