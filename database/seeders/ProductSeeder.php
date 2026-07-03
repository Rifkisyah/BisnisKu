<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Supplier;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $suppliers = Supplier::all();
        if ($suppliers->isEmpty()) return;

        $products = [
            ['name' => 'iPhone 13 128GB', 'category' => 'Smartphone Baru'],
            ['name' => 'Samsung Galaxy A54 5G', 'category' => 'Smartphone Baru'],
            ['name' => 'Xiaomi Redmi Note 12', 'category' => 'Smartphone Baru'],
            ['name' => 'Oppo Reno 10', 'category' => 'Smartphone Baru'],
            ['name' => 'Vivo V27', 'category' => 'Smartphone Baru'],
            ['name' => 'Charger Vivan 18W Fast Charging', 'category' => 'Charger & Kabel'],
            ['name' => 'Kabel Data Robot Type C', 'category' => 'Charger & Kabel'],
            ['name' => 'TWS Baseus WM01', 'category' => 'Earphone & TWS'],
            ['name' => 'Powerbank Robot 10000mAh', 'category' => 'Powerbank'],
            ['name' => 'Earphone JBL C150SI', 'category' => 'Earphone & TWS'],
            ['name' => 'Tempered Glass iPhone 13', 'category' => 'Screen Protector'],
            ['name' => 'Casing Silikon Samsung A54', 'category' => 'Casing & Cover'],
            ['name' => 'Baterai Original iPhone 11', 'category' => 'Baterai'],
            ['name' => 'LCD Xiaomi Redmi Note 10', 'category' => 'LCD & Touchscreen'],
            ['name' => 'Konektor Charger Oppo A3s', 'category' => 'Fleksibel & Konektor'],
            ['name' => 'Voucher Telkomsel 10GB', 'category' => 'Voucher Internet'],
            ['name' => 'Perdana Indosat 20GB', 'category' => 'Perdana & Nomor Cantik'],
            ['name' => 'Pulsa 50 Ribu', 'category' => 'Pulsa Elektrik'],
            ['name' => 'SD Card SanDisk 64GB', 'category' => 'Aksesoris Lainnya'],
            ['name' => 'Flashdisk Kingston 32GB', 'category' => 'Aksesoris Lainnya']
        ];

        foreach ($products as $i => $p) {
            $category = Category::where('name', $p['category'])->first();
            $category_code = $category ? $category->category_code : null;

            if (!$category_code) {
                // Fallback to random if category deleted
                $cat = Category::where('type', 'product')->inRandomOrder()->first();
                if ($cat) $category_code = $cat->category_code;
            }

            if (!$category_code) continue;

            $purchase = $faker->numberBetween(5, 500) * 10000;
            Product::firstOrCreate(
                ['product_code' => 'PRD' . str_pad($i + 1, 5, '0', STR_PAD_LEFT)],
                [
                    'store_id'      => $store?->id,
                    'name'          => $p['name'],
                    'category_code' => $category_code,
                    'supplier_code' => $suppliers->random()->supplier_code,
                    'purchase_price' => $purchase,
                    'selling_price' => $purchase + $faker->numberBetween(1, 20) * 10000,
                    'stock'         => $faker->numberBetween(10, 100),
                    'minimum_stock' => 5,
                    'type'          => 'physical',
                    'status'        => 'active',
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}
