<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom server_key dan client_key ke payment_settings.
     * Ini memungkinkan setiap toko punya API key Midtrans sendiri
     * (tidak bergantung pada .env global).
     */
    public function up(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('payment_settings', 'server_key')) {
                $table->string('server_key')->nullable()->after('merchant_id');
            }
            if (!Schema::hasColumn('payment_settings', 'client_key')) {
                $table->string('client_key')->nullable()->after('server_key');
            }
            if (!Schema::hasColumn('payment_settings', 'qris_acquirer')) {
                $table->string('qris_acquirer')->nullable()->default('gopay')->after('client_key');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->dropColumn(['server_key', 'client_key', 'qris_acquirer']);
        });
    }
};
