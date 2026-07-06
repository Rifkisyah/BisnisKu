<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add columns to product_purchase_items (ALREADY RUN)
        /*
        Schema::table('product_purchase_items', function (Blueprint $table) {
            $table->enum('source', ['whatsapp', 'marketplace', 'offline', 'other', 'service'])->nullable()->after('product_code');
            $table->string('supplier_code')->nullable()->after('source');
            $table->string('marketplace_name', 100)->nullable()->after('supplier_code');
            $table->string('marketplace_seller', 100)->nullable()->after('marketplace_name');
            $table->string('marketplace_order_id', 100)->nullable()->after('marketplace_seller');
            $table->string('marketplace_notes', 500)->nullable()->after('marketplace_order_id');
            $table->string('store_name', 100)->nullable()->after('marketplace_notes');
            $table->string('receipt_number', 50)->nullable()->after('store_name');
            $table->string('offline_notes', 500)->nullable()->after('receipt_number');
            $table->string('other_source', 100)->nullable()->after('offline_notes');
            $table->string('other_notes', 500)->nullable()->after('other_source');
        });
        */

        // 2. Copy existing data from product_purchases to product_purchase_items (ALREADY RUN)
        /*
        DB::statement("
            UPDATE product_purchase_items ppi
            JOIN product_purchases pp ON ppi.product_purchase_code = pp.product_purchase_code
            SET 
                ppi.source = pp.source,
                ppi.supplier_code = pp.supplier_code,
                ppi.marketplace_name = pp.marketplace_name,
                ppi.marketplace_seller = pp.marketplace_seller,
                ppi.marketplace_order_id = pp.marketplace_order_id,
                ppi.marketplace_notes = pp.marketplace_notes,
                ppi.store_name = pp.store_name,
                ppi.receipt_number = pp.receipt_number,
                ppi.offline_notes = pp.offline_notes,
                ppi.other_source = pp.other_source,
                ppi.other_notes = pp.other_notes
        ");
        */

        // 3. Drop columns from product_purchases
        Schema::table('product_purchases', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['supplier_code']);
            }
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropColumn([
                    'source',
                    'supplier_code',
                    'wa_message_content',
                    'wa_message_status',
                    'marketplace_name',
                    'marketplace_seller',
                    'marketplace_order_id',
                    'marketplace_notes',
                    'store_name',
                    'receipt_number',
                    'offline_notes',
                    'other_source',
                    'other_notes'
                ]);
            }
        });
        
        // 4. Add foreign key to product_purchase_items
        Schema::table('product_purchase_items', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite' && Schema::hasColumn('product_purchase_items', 'supplier_code')) {
                $table->foreign('supplier_code')->references('supplier_code')->on('suppliers')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Re-add columns to product_purchases
        Schema::table('product_purchases', function (Blueprint $table) {
            $table->enum('source', ['whatsapp', 'marketplace', 'offline', 'other', 'service'])->nullable()->after('store_id');
            $table->string('supplier_code')->nullable()->after('source');
            $table->string('wa_message_content', 2000)->nullable()->after('created_by');
            $table->enum('wa_message_status', ['pending', 'sent', 'failed'])->nullable()->after('wa_message_content');
            $table->string('marketplace_name', 100)->nullable()->after('wa_message_status');
            $table->string('marketplace_seller', 100)->nullable()->after('marketplace_name');
            $table->string('marketplace_order_id', 100)->nullable()->after('marketplace_seller');
            $table->string('marketplace_notes', 500)->nullable()->after('marketplace_order_id');
            $table->string('store_name', 100)->nullable()->after('marketplace_notes');
            $table->string('receipt_number', 50)->nullable()->after('store_name');
            $table->string('offline_notes', 500)->nullable()->after('receipt_number');
            $table->string('other_source', 100)->nullable()->after('offline_notes');
            $table->string('other_notes', 500)->nullable()->after('other_source');
            
            $table->foreign('supplier_code')->references('supplier_code')->on('suppliers')->nullOnDelete();
        });

        // 2. We cannot reliably copy back if items had different sources, but we can take the first item's source
        DB::statement("
            UPDATE product_purchases pp
            JOIN (
                SELECT product_purchase_code, MIN(id) as first_item_id
                FROM product_purchase_items
                GROUP BY product_purchase_code
            ) first_items ON pp.product_purchase_code = first_items.product_purchase_code
            JOIN product_purchase_items ppi ON first_items.first_item_id = ppi.id
            SET 
                pp.source = ppi.source,
                pp.supplier_code = ppi.supplier_code,
                pp.marketplace_name = ppi.marketplace_name,
                pp.marketplace_seller = ppi.marketplace_seller,
                pp.marketplace_order_id = ppi.marketplace_order_id,
                pp.marketplace_notes = ppi.marketplace_notes,
                pp.store_name = ppi.store_name,
                pp.receipt_number = ppi.receipt_number,
                pp.offline_notes = ppi.offline_notes,
                pp.other_source = ppi.other_source,
                pp.other_notes = ppi.other_notes
        ");

        // 3. Drop columns from product_purchase_items
        Schema::table('product_purchase_items', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['supplier_code']);
            }
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropColumn([
                    'source',
                    'supplier_code',
                    'marketplace_name',
                    'marketplace_seller',
                    'marketplace_order_id',
                    'marketplace_notes',
                    'store_name',
                    'receipt_number',
                    'offline_notes',
                    'other_source',
                    'other_notes'
                ]);
            }
        });
    }
};
