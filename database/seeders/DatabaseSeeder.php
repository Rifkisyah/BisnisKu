<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles are global (not per-store) — seed first
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'owner']);
        Role::firstOrCreate(['name' => 'kasir']);
        Role::firstOrCreate(['name' => 'teknisi']);
        Role::firstOrCreate(['name' => 'gudang']);

        $this->call([
            // 2. Store & Users (UMKM Mikro Konter: Armal Cellular)
            StoreSeeder::class,
            UserSeeder::class,

            // 3. Store Settings & Payment Settings
            SettingSeeder::class,
            PaymentSettingSeeder::class,

            // 4. Tenant-scoped Master Data (10 Categories, 5 Suppliers, 24 Products)
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,

            // 5. Operational Data (Transactions, Services, Debts, Purchases, Stock Movements, Payments)
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
