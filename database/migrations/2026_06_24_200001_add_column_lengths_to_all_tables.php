<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enforce maximum character lengths on all user-input string columns.
 * This prevents oversized data at the database level and aligns with
 * the application-layer validation rules (min/max).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── categories ────────────────────────────────────────────────────
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name', 60)->change();
            $table->string('description', 300)->nullable()->change();
        });

        // ── suppliers ─────────────────────────────────────────────────────
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('email', 150)->nullable()->change();
            $table->string('address', 300)->nullable()->change();
            $table->string('notes', 500)->nullable()->change();
        });

        // ── products ──────────────────────────────────────────────────────
        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('description', 500)->nullable()->change();
        });

        // ── users ─────────────────────────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 60)->change();
            $table->string('email', 150)->change();
            $table->string('contact', 20)->nullable()->change();
        });

        // ── service_repairs ───────────────────────────────────────────────
        Schema::table('service_repairs', function (Blueprint $table) {
            $table->string('customer_name', 100)->change();
            $table->string('customer_phone', 20)->nullable()->change();
            $table->string('notes', 500)->nullable()->change();
        });

        // ── service_repair_items ──────────────────────────────────────────
        Schema::table('service_repair_items', function (Blueprint $table) {
            $table->string('name', 100)->change();
            $table->string('brand', 60)->nullable()->change();
            $table->string('series', 60)->nullable()->change();
            // complaint and diagnosis_result remain as text (no length change needed for text)
        });

        // ── debts ─────────────────────────────────────────────────────────
        Schema::table('debts', function (Blueprint $table) {
            $table->string('debtor_name', 100)->change();
            $table->string('debtor_contact', 20)->nullable()->change();
            $table->string('debtor_address', 300)->nullable()->change();
            $table->string('notes', 500)->nullable()->change();
        });

        // ── product_purchase_items ────────────────────────────────────────
        Schema::table('product_purchase_items', function (Blueprint $table) {
            $table->string('temp_product_name', 100)->nullable()->change();
        });

        // ── product_purchases ─────────────────────────────────────────────
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->string('marketplace_name', 100)->nullable()->change();
            $table->string('marketplace_seller', 100)->nullable()->change();
            $table->string('marketplace_order_id', 100)->nullable()->change();
            $table->string('marketplace_notes', 500)->nullable()->change();
            $table->string('store_name', 100)->nullable()->change();
            $table->string('receipt_number', 50)->nullable()->change();
            $table->string('offline_notes', 500)->nullable()->change();
            $table->string('other_source', 100)->nullable()->change();
            $table->string('other_notes', 500)->nullable()->change();
            $table->string('notes', 500)->nullable()->change();
            $table->string('wa_message_content', 2000)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Revert all columns back to default 255 length
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('email', 255)->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->text('notes')->nullable()->change();
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->text('description')->nullable()->change();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 255)->change();
            $table->string('email', 255)->change();
            $table->string('contact', 50)->nullable()->change();
        });

        Schema::table('service_repairs', function (Blueprint $table) {
            $table->string('customer_name', 255)->change();
            $table->string('customer_phone', 255)->nullable()->change();
            $table->text('notes')->nullable()->change();
        });

        Schema::table('service_repair_items', function (Blueprint $table) {
            $table->string('name', 255)->change();
            $table->string('brand', 100)->nullable()->change();
            $table->string('series', 100)->nullable()->change();
            // complaint and diagnosis_result were text, no change needed
        });

        Schema::table('debts', function (Blueprint $table) {
            $table->string('debtor_name', 255)->change();
            $table->string('debtor_contact', 50)->nullable()->change();
            $table->text('debtor_address')->nullable()->change();
            $table->text('notes')->nullable()->change();
        });

        Schema::table('product_purchase_items', function (Blueprint $table) {
            $table->string('temp_product_name', 255)->nullable()->change();
        });

        Schema::table('product_purchases', function (Blueprint $table) {
            $table->string('marketplace_name', 255)->nullable()->change();
            $table->string('marketplace_seller', 255)->nullable()->change();
            $table->string('marketplace_order_id', 255)->nullable()->change();
            $table->text('marketplace_notes')->nullable()->change();
            $table->string('store_name', 255)->nullable()->change();
            $table->string('receipt_number', 255)->nullable()->change();
            $table->text('offline_notes')->nullable()->change();
            $table->string('other_source', 255)->nullable()->change();
            $table->text('other_notes')->nullable()->change();
            $table->text('notes')->nullable()->change();
            $table->text('wa_message_content')->nullable()->change();
        });
    }
};
