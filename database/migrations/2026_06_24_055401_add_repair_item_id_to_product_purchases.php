<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('repair_item_id')->nullable()->after('supplier_code');
            $table->foreign('repair_item_id')->references('id')->on('service_repair_items')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->dropForeign(['repair_item_id']);
            $table->dropColumn('repair_item_id');
        });
    }
};
