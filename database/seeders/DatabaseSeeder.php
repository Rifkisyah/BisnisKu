<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles are global (not per-store) — seed first
        Role::firstOrCreate(['name' => 'owner']);
        Role::firstOrCreate(['name' => 'kasir']);
        Role::firstOrCreate(['name' => 'teknisi']);
        Role::firstOrCreate(['name' => 'gudang']);

        $this->call([
            // 2. Stores + owners (must be before all other tenant-scoped data)
            StoreSeeder::class,

            // 3. Tenant-scoped data (all scoped to Armal Cellular / store 1 by default)
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            TransactionSeeder::class,
            ServiceRepairSeeder::class,
            DebtSeeder::class,
            DebtPaymentSeeder::class,
            ProductPurchaseSeeder::class,
            StockMovementSeeder::class,

            PaymentSeeder::class,
        ]);
    }
}

