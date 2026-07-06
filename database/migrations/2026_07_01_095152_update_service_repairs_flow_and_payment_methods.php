<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add final_payment_method column
        Schema::table('service_repairs', function (Blueprint $table) {
            $table->enum('final_payment_method', ['cash', 'transfer', 'qris'])->nullable()->after('payment_method');
        });

        // Change default status to 'diagnosing'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE service_repairs MODIFY COLUMN status ENUM(
                'draft','waiting_dp','diagnosing','waiting_parts','repairing','ready','done','cancelled'
            ) DEFAULT 'diagnosing'");
        }
    }

    public function down(): void
    {
        Schema::table('service_repairs', function (Blueprint $table) {
            $table->dropColumn('final_payment_method');
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE service_repairs MODIFY COLUMN status ENUM(
                'draft','waiting_dp','diagnosing','waiting_parts','repairing','ready','done','cancelled'
            ) DEFAULT 'draft'");
        }
    }
};