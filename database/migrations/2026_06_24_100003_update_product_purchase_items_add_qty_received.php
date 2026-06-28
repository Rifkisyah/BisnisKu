<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_purchase_items', function (Blueprint $table) {
            // Jumlah yang benar-benar diterima (≤ quantity)
            $table->integer('quantity_received')->default(0)->after('quantity');
            // Jumlah yang ditolak / rusak
            $table->integer('quantity_rejected')->default(0)->after('quantity_received');
            // Catatan untuk barang yang ditolak
            $table->text('rejection_notes')->nullable()->after('quantity_rejected');
        });
    }

    public function down(): void
    {
        Schema::table('product_purchase_items', function (Blueprint $table) {
            $table->dropColumn(['quantity_received', 'quantity_rejected', 'rejection_notes']);
        });
    }
};
