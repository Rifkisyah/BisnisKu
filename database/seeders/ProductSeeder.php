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

            $purchase = $faker->numberBetween(10, 150) * 1000; // 10k to 150k
            Product::firstOrCreate(
                ['product_code' => 'PRD' . str_pad($i + 1, 5, '0', STR_PAD_LEFT)],
                [
                    'store_id'      => $store?->id,
                    'name'          => $p['name'],
                    'category_code' => $category_code,
                    'supplier_code' => $suppliers->random()->supplier_code,
                    'purchase_price' => $purchase,
                    'selling_price' => $purchase + $faker->numberBetween(5, 50) * 1000,
                    'stock'         => $faker->numberBetween(10, 100),
                    'minimum_stock' => 5,
                    'type'          => 'physical',
                    'status'        => 'active',
                ]
            );
        }

        // Generate additional products to reach at least 100
        $extraProducts = [
            'Samsung Galaxy S23 Ultra', 'Samsung Galaxy A34 5G', 'Samsung Galaxy Z Flip 5', 'iPhone 14 Pro Max', 'iPhone 15 128GB',
            'Xiaomi 13T', 'Poco X5 Pro', 'Redmi Note 12 Pro', 'Oppo Find N2 Flip', 'Oppo Reno 8 T',
            'Vivo V29 5G', 'Vivo Y36', 'Realme 11 Pro+', 'Realme C55', 'Infinix Note 30 Pro',
            'Infinix Zero 30', 'Tecno Pova 5 Pro', 'Itel S23+', 'Asus ROG Phone 7', 'Sony Xperia 1 V',
            'iPad Air 5', 'Samsung Galaxy Tab S9', 'Xiaomi Pad 6', 'Huawei MatePad 11', 'Apple Watch Series 9',
            'Samsung Galaxy Watch 6', 'Garmin Forerunner 265', 'Amazfit GTR 4', 'TWS AirPods Pro 2', 'TWS Galaxy Buds 2 Pro',
            'TWS Sony WF-1000XM5', 'TWS Soundcore Liberty 4', 'Jabra Elite 8 Active', 'Speaker JBL Flip 6', 'Speaker Harman Kardon Onyx 7',
            'Speaker Marshall Emberton', 'Powerbank Anker PowerCore 20000', 'Powerbank Aukey 10000mAh', 'Powerbank Xiaomi 3 10000', 'Charger Anker Nano 3 30W',
            'Charger Ugreen 65W GaN', 'Charger Baseus 100W', 'Kabel Anker PowerLine III', 'Kabel Ugreen Type C 100W', 'Kabel Baseus Lightning 20W',
            'SanDisk Ultra MicroSD 128GB', 'Samsung Evo Plus 256GB', 'Flashdisk SanDisk Cruzer 64GB', 'OTG Type C to USB A', 'Spigen Tough Armor iPhone 14',
            'Ringke Fusion Samsung S23', 'Nillkin CamShield Poco X5', 'Baterai Vizz iPhone X', 'Baterai Hippo Samsung Note 9', 'Baterai Rakitan Oppo A3s',
            'LCD OEM iPhone 11 Pro', 'LCD Copotan Samsung A51', 'Touchscreen Xiaomi Note 8', 'Kamera Belakang iPhone 12', 'Kamera Depan Vivo Y12',
            'Konektor Charger Realme 5i', 'Fleksibel On/Off Oppo F9', 'IC Power MT6357CRV', 'Lem B7000 50ml', 'Obeng Set Qianli',
            'Flux Amtech NC-559', 'Solder Wick Goot', 'Timah Mechanic 0.3mm', 'Blower Quick 850A', 'Multitester Fluke 101',
            'Voucher Telkomsel 50 Ribu', 'Voucher Indosat 100 Ribu', 'Perdana XL 30GB', 'Perdana Tri 66GB', 'Smartfren Kuota 50GB',
            'Casing Custom Akrilik', 'Tempered Glass Mocolo', 'Hydrogel Rock Space', 'Skin Garskin Carbon', 'Strap Apple Watch 44mm'
        ];

        $allCategories = Category::where('type', 'product')->get();
        if ($allCategories->isNotEmpty()) {
            for ($i = 21; $i <= 100; $i++) {
                $idx = $i - 21;
                $name = $extraProducts[$idx] ?? ('Produk Aksesoris ' . $idx); 
                
                $purchase = $faker->numberBetween(10, 150) * 1000; // 10k to 150k
                Product::firstOrCreate(
                    ['product_code' => 'PRD' . str_pad($i, 5, '0', STR_PAD_LEFT)],
                    [
                        'store_id'      => $store?->id,
                        'name'          => $name,
                        'category_code' => $allCategories->random()->category_code,
                        'supplier_code' => $suppliers->random()->supplier_code,
                        'purchase_price' => $purchase,
                        'selling_price' => $purchase + $faker->numberBetween(5, 50) * 1000, // 5k to 50k profit
                        'stock'         => $faker->numberBetween(10, 100),
                        'minimum_stock' => 5,
                        'type'          => 'physical',
                        'status'        => 'active',
                    ]
                );
            }
        }

        app()->forgetInstance('current_store');
    }
}
