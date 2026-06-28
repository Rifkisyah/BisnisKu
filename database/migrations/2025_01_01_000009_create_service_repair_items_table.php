<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_repair_items', function (Blueprint $table) {
            $table->id();
            $table->string('repair_code');
            $table->foreign('repair_code')->references('repair_code')->on('service_repairs')->onDelete('cascade');
            // Component product (optional — sparepart yang diambil dari stok)
            $table->string('component_code')->nullable();
            $table->foreign('component_code')->references('product_code')->on('products')->onDelete('set null');
            // Item description
            $table->string('name'); // nama perangkat / sparepart / tindakan
            $table->string('brand')->nullable();
            $table->string('series')->nullable();
            $table->text('complaint')->nullable();       // keluhan pelanggan
            $table->text('diagnosis_result')->nullable(); // hasil diagnosa teknisi
            // Pricing
            $table->integer('quantity')->default(1);
            $table->decimal('service_fee', 15, 2)->default(0); // biaya jasa untuk item ini
            $table->decimal('subtotal', 15, 2)->default(0);    // harga komponen/part
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_repair_items');
    }
};
