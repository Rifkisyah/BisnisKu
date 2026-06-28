<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_purchases', function (Blueprint $table) {
            $table->string('product_purchase_code')->primary();
            $table->enum('source', ['whatsapp', 'marketplace', 'offline', 'other'])->default('other');
            // Supplier (opsional - hanya untuk WA / jika diketahui)
            $table->string('supplier_code')->nullable();
            $table->foreign('supplier_code')->references('supplier_code')->on('suppliers')->onDelete('set null');
            $table->date('purchase_date');
            $table->decimal('total', 15, 2)->default(0);
            $table->enum('status', ['draft', 'ordered', 'received', 'canceled'])->default('draft');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');

            // WhatsApp source fields
            $table->text('wa_message_content')->nullable();
            $table->enum('wa_message_status', ['pending', 'sent', 'failed'])->nullable();

            // Marketplace source fields
            $table->string('marketplace_name')->nullable();
            $table->string('marketplace_seller')->nullable();
            $table->string('marketplace_order_id')->nullable();
            $table->text('marketplace_notes')->nullable();

            // Offline store fields
            $table->string('store_name')->nullable();
            $table->string('receipt_number')->nullable();
            $table->text('offline_notes')->nullable();

            // Other source fields
            $table->string('other_source')->nullable();
            $table->text('other_notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_purchases');
    }
};
