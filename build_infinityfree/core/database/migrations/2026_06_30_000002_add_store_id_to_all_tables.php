<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add store_id to all tenant-scoped tables.
     * Uses nullable() so existing rows remain valid after migration.
     * After fresh seed, store_id will always be set.
     */
    public function up(): void
    {
        // --- users: each user (owner/staff) belongs to one store ---
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- Add owner_id FK to stores now that users table is ready ---
        Schema::table('stores', function (Blueprint $table) {
            $table->foreign('owner_id')->references('id')->on('users')->onDelete('set null');
        });

        // --- categories ---
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('category_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- suppliers ---
        Schema::table('suppliers', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('supplier_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- products ---
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('product_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- transactions ---
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('transaction_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- service_repairs ---
        Schema::table('service_repairs', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('repair_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- debts ---
        Schema::table('debts', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('debt_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- product_purchases (procurements) ---
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('product_purchase_code');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- stock_movements ---
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- product_analytics ---
        Schema::table('product_analytics', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- settings: change from global key→value to per-store ---
        // Drop unique on 'key', add store_id, re-add unique on (store_id, key)
        Schema::table('settings', function (Blueprint $table) {
            $table->dropUnique(['key']);
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
            $table->unique(['store_id', 'key']);
        });

        // --- payment_settings ---
        Schema::table('payment_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });

        // --- payments ---
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->nullable()->after('id');
            $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        $tables = [
            'payments'          => ['store_id'],
            'payment_settings'  => ['store_id'],
            'settings'          => ['store_id'],
            'product_analytics' => ['store_id'],
            'stock_movements'   => ['store_id'],
            'product_purchases' => ['store_id'],
            'debts'             => ['store_id'],
            'service_repairs'   => ['store_id'],
            'transactions'      => ['store_id'],
            'products'          => ['store_id'],
            'suppliers'         => ['store_id'],
            'categories'        => ['store_id'],
        ];

        foreach ($tables as $table => $columns) {
            Schema::table($table, function (Blueprint $t) use ($table, $columns) {
                $t->dropForeign([$columns[0]]);
                $t->dropColumn($columns);
            });
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->unique('key');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn('store_id');
        });
    }
};
