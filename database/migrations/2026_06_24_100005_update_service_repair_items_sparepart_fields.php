<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_repair_items', function (Blueprint $table) {
            // Add parent_id if not already exists
            if (!Schema::hasColumn('service_repair_items', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('id');
                $table->foreign('parent_id')->references('id')->on('service_repair_items')->onDelete('cascade');
            }

            // Add images if not already exists
            if (!Schema::hasColumn('service_repair_items', 'images')) {
                $table->json('images')->nullable()->after('diagnosis_result');
            }

            // Add sparepart_type: null = device item, 'from_stock' = ambil dari stok, 'requested' = pengajuan
            $table->enum('sparepart_type', ['from_stock', 'requested'])->nullable()->after('series');

            // Status pengadaan sparepart jika mode 'requested'
            $table->enum('sparepart_status', ['pending', 'available', 'used'])->nullable()->after('sparepart_type');

            // Harga beli sementara (saat pengajuan, sebelum diterima)
            $table->decimal('temp_purchase_price', 15, 2)->nullable()->after('sparepart_status');
        });
    }

    public function down(): void
    {
        Schema::table('service_repair_items', function (Blueprint $table) {
            $table->dropColumn(['sparepart_type', 'sparepart_status', 'temp_purchase_price']);
        });
    }
};
