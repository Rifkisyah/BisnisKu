<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('product_code');
            $table->foreign('product_code')->references('product_code')->on('products')->onDelete('cascade');
            $table->integer('total_qty_sold')->default(0);
            $table->integer('transaction_frequency')->default(0);
            $table->integer('remaining_stock')->default(0);
            $table->string('cluster_label')->nullable(); // fast_moving, medium_moving, slow_moving
            $table->decimal('sma_value', 15, 2)->nullable();
            $table->integer('predicted_demand')->nullable();
            $table->integer('restock_recommendation')->nullable();
            $table->date('analysis_date');
            $table->timestamps();

            $table->index(['product_code', 'analysis_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_analytics');
    }
};
