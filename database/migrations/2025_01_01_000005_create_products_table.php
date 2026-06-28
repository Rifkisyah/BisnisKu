<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('product_code')->primary();
            $table->string('name');
            $table->string('category_code');
            $table->foreign('category_code')->references('category_code')->on('categories')->onDelete('restrict');
            $table->string('supplier_code')->nullable();
            $table->foreign('supplier_code')->references('supplier_code')->on('suppliers')->onDelete('set null');
            $table->decimal('purchase_price', 15, 2)->default(0);
            $table->decimal('selling_price', 15, 2)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(5);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->enum('type', ['physical', 'digital', 'sparepart'])->default('physical');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
