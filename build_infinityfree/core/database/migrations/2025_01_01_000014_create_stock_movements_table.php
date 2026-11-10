<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->foreign('product_code')->references('product_code')->on('products')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->enum('type', ['in', 'out', 'adjustment']);
            $table->integer('total_stock');
            $table->integer('previous_stock');
            $table->integer('current_stock');
            $table->dateTime('movement_date');
            $table->string('reference_type')->nullable();
            $table->string('reference_code')->nullable(); // String karena PK sekarang menggunakan code
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_code', 'movement_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
