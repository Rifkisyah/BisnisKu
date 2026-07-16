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
        Schema::dropIfExists('product_analytics');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('product_analytics', function (Blueprint $table) {
            $table->id();
            $table->string('product_code', 50);
            $table->string('category_code', 50)->nullable();
            
            // K-Means Metrics
            $table->integer('transaction_frequency')->default(0);
            $table->integer('total_qty_sold')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            
            $table->string('cluster_label', 20)->nullable(); 
            
            // SMA
            $table->date('forecast_date')->nullable();
            $table->decimal('forecast_qty', 10, 2)->nullable();
            
            $table->timestamps();

            $table->foreign('product_code')->references('product_code')->on('products')->onDelete('cascade');
        });
    }
};
