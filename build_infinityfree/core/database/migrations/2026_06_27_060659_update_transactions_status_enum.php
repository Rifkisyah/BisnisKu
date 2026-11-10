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
        // Add new values to enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('completed', 'canceled', 'pending', 'unpaid', 'paid', 'cancelled') DEFAULT 'unpaid'");
        }
        // Map existing values
        DB::statement("UPDATE transactions SET status = 'paid' WHERE status = 'completed'");
        DB::statement("UPDATE transactions SET status = 'unpaid' WHERE status = 'pending'");
        DB::statement("UPDATE transactions SET status = 'cancelled' WHERE status = 'canceled'");
        // Restrict enum to new values
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('unpaid', 'paid', 'cancelled') DEFAULT 'unpaid'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('completed', 'canceled', 'pending', 'unpaid', 'paid', 'cancelled') DEFAULT 'pending'");
        }
        DB::statement("UPDATE transactions SET status = 'completed' WHERE status = 'paid'");
        DB::statement("UPDATE transactions SET status = 'pending' WHERE status = 'unpaid'");
        DB::statement("UPDATE transactions SET status = 'canceled' WHERE status = 'cancelled'");
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('completed', 'canceled', 'pending') DEFAULT 'completed'");
        }
    }
};
