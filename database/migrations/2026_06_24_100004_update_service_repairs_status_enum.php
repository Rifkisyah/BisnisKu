<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: Expand enum with ALL old + new values to allow data migration
        DB::statement("ALTER TABLE service_repairs MODIFY COLUMN status ENUM(
            'received','in_progress','waiting_for_parts','completed','canceled','picked_up',
            'draft','waiting_dp','diagnosing','waiting_parts','repairing','ready','done','cancelled'
        ) DEFAULT 'received'");

        // Step 2: Migrate old statuses to new ones
        DB::statement("UPDATE service_repairs SET status = 'diagnosing'    WHERE status = 'received'");
        DB::statement("UPDATE service_repairs SET status = 'repairing'     WHERE status = 'in_progress'");
        DB::statement("UPDATE service_repairs SET status = 'waiting_parts' WHERE status = 'waiting_for_parts'");
        DB::statement("UPDATE service_repairs SET status = 'done'          WHERE status = 'completed'");
        DB::statement("UPDATE service_repairs SET status = 'done'          WHERE status = 'picked_up'");
        DB::statement("UPDATE service_repairs SET status = 'cancelled'     WHERE status = 'canceled'");

        // Step 3: Narrow enum to only new values
        DB::statement("ALTER TABLE service_repairs MODIFY COLUMN status ENUM(
            'draft','waiting_dp','diagnosing','waiting_parts','repairing','ready','done','cancelled'
        ) DEFAULT 'draft'");

        // Add customer_phone if it doesn't exist
        if (!Schema::hasColumn('service_repairs', 'customer_phone')) {
            Schema::table('service_repairs', function (Blueprint $table) {
                $table->string('customer_phone')->nullable()->after('customer_name');
            });
        }

        // Make technician_id nullable (kasir bisa buat tiket, teknisi assign nanti)
        Schema::table('service_repairs', function (Blueprint $table) {
            $table->unsignedBigInteger('technician_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE service_repairs MODIFY COLUMN status ENUM(
            'received','in_progress','waiting_for_parts','completed','canceled','picked_up'
        ) DEFAULT 'received'");
    }
};
