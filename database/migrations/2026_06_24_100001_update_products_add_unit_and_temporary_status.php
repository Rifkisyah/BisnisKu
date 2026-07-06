<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add unit column
        Schema::table('products', function (Blueprint $table) {
            $table->string('unit', 50)->default('pcs')->after('minimum_stock');
        });

        // Change status enum to include 'temporary'
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive', 'temporary') DEFAULT 'active'");
        }
    }

    public function down(): void
    {
        // Revert status enum
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE products MODIFY COLUMN status ENUM('active', 'inactive') DEFAULT 'active'");
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('unit');
        });
    }
};
