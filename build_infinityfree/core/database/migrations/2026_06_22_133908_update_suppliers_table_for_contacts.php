<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('phone_prefix', 10)->nullable()->after('name');
            $table->string('phone_number', 20)->nullable()->after('phone_prefix');
            $table->string('image')->nullable()->after('is_active');
            $table->dropColumn('contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('contact')->nullable()->after('name');
            $table->dropColumn(['phone_prefix', 'phone_number', 'image']);
        });
    }
};
