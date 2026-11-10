<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds qr_string (EMV QRIS string from Midtrans) and expiry_time columns.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->text('qr_string')->nullable()->after('external_order_id');
            $table->timestamp('expiry_time')->nullable()->after('qr_string');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['qr_string', 'expiry_time']);
        });
    }
};
