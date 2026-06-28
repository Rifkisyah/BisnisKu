<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->string('product_purchase_code');
            $table->foreign('product_purchase_code')->references('product_purchase_code')->on('product_purchases')->onDelete('cascade');
            // Produk — bisa null jika belum ada di sistem (pakai temp_product_name)
            $table->string('product_code')->nullable();
            $table->foreign('product_code')->references('product_code')->on('products')->onDelete('set null');
            // Nama produk sementara jika produk belum ada di sistem
            $table->string('temp_product_name')->nullable();
            // Apakah item ini sudah dihubungkan ke produk saat diterima
            $table->boolean('is_resolved')->default(false);
            // Produk yang dipakai saat resolve (bisa berbeda dari product_code asli)
            $table->string('resolved_product_code')->nullable();
            $table->foreign('resolved_product_code')->references('product_code')->on('products')->onDelete('set null');
            $table->integer('quantity');
            $table->decimal('purchase_price', 15, 2);
            $table->decimal('subtotal', 15, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_purchase_items');
    }
};
