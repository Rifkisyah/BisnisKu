<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand enum to include BOTH 'canceled' (old) and 'cancelled' (new) + new values
        // This prevents data truncation error during the transition
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE product_purchases MODIFY COLUMN status ENUM('draft', 'ordered', 'partial_received', 'received', 'canceled', 'cancelled') DEFAULT 'draft'");
        }

        // Step 2: Migrate old 'canceled' data to 'cancelled'
        DB::statement("UPDATE product_purchases SET status = 'cancelled' WHERE status = 'canceled'");

        // Step 3: Now narrow the enum to remove old 'canceled'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE product_purchases MODIFY COLUMN status ENUM('draft', 'ordered', 'partial_received', 'received', 'cancelled') DEFAULT 'draft'");
        }

        // Step 4: Update source enum to include 'service'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE product_purchases MODIFY COLUMN source ENUM('whatsapp', 'marketplace', 'offline', 'other', 'service') DEFAULT 'other'");
        }

        // Add estimated_arrival_date if not already there (may exist from prior migration)
        if (!Schema::hasColumn('product_purchases', 'estimated_arrival_date')) {
            Schema::table('product_purchases', function (Blueprint $table) {
                $table->date('estimated_arrival_date')->nullable()->after('purchase_date');
            });
        }

        // Add repair_item_id if not already there
        if (!Schema::hasColumn('product_purchases', 'repair_item_id')) {
            Schema::table('product_purchases', function (Blueprint $table) {
                $table->unsignedBigInteger('repair_item_id')->nullable()->after('notes');
                $table->foreign('repair_item_id')->references('id')->on('service_repair_items')->onDelete('set null');
            });
        }

        // Add partial_notes
        if (!Schema::hasColumn('product_purchases', 'partial_notes')) {
            Schema::table('product_purchases', function (Blueprint $table) {
                $table->text('partial_notes')->nullable()->after('notes');
            });
        }
    }

    public function down(): void
    {
        DB::statement("UPDATE product_purchases SET status = 'canceled' WHERE status = 'cancelled'");
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE product_purchases MODIFY COLUMN status ENUM('draft', 'ordered', 'received', 'canceled') DEFAULT 'draft'");
            DB::statement("ALTER TABLE product_purchases MODIFY COLUMN source ENUM('whatsapp', 'marketplace', 'offline', 'other') DEFAULT 'other'");
        }

        if (Schema::hasColumn('product_purchases', 'partial_notes')) {
            Schema::table('product_purchases', function (Blueprint $table) {
                $table->dropColumn('partial_notes');
            });
        }
    }
};
