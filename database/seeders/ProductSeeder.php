<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Physical Products
        $productDefs = [
            ['product_code' => 'PRD00001', 'name' => 'Samsung Galaxy A15',       'category_code' => 'CAT0001',  'supplier_code' => 'SUP0001', 'purchase_price' => 2200000, 'selling_price' => 2500000, 'stock' => 15,  'minimum_stock' => 3,  'type' => 'physical'],
            ['product_code' => 'PRD00002', 'name' => 'Xiaomi Redmi Note 13',      'category_code' => 'CAT0001',  'supplier_code' => 'SUP0001', 'purchase_price' => 2500000, 'selling_price' => 2850000, 'stock' => 10,  'minimum_stock' => 3,  'type' => 'physical'],
            ['product_code' => 'PRD00003', 'name' => 'OPPO A18',                  'category_code' => 'CAT0001',  'supplier_code' => 'SUP0001', 'purchase_price' => 1800000, 'selling_price' => 2100000, 'stock' => 8,   'minimum_stock' => 3,  'type' => 'physical'],
            ['product_code' => 'PRD00004', 'name' => 'Vivo Y17s',                 'category_code' => 'CAT0001',  'supplier_code' => 'SUP0001', 'purchase_price' => 1900000, 'selling_price' => 2200000, 'stock' => 12,  'minimum_stock' => 3,  'type' => 'physical'],
            ['product_code' => 'PRD00005', 'name' => 'Casing Samsung A15',        'category_code' => 'CAT0002',  'supplier_code' => 'SUP0002', 'purchase_price' => 15000,   'selling_price' => 35000,   'stock' => 50,  'minimum_stock' => 10, 'type' => 'physical'],
            ['product_code' => 'PRD00006', 'name' => 'Tempered Glass Universal',  'category_code' => 'CAT0002',  'supplier_code' => 'SUP0002', 'purchase_price' => 5000,    'selling_price' => 15000,   'stock' => 100, 'minimum_stock' => 20, 'type' => 'physical'],
            ['product_code' => 'PRD00007', 'name' => 'Charger Fast Charging 33W', 'category_code' => 'CAT0002',  'supplier_code' => 'SUP0002', 'purchase_price' => 25000,   'selling_price' => 55000,   'stock' => 30,  'minimum_stock' => 5,  'type' => 'physical'],
            ['product_code' => 'PRD00008', 'name' => 'Kabel Data Type-C',         'category_code' => 'CAT0002',  'supplier_code' => 'SUP0002', 'purchase_price' => 8000,    'selling_price' => 20000,   'stock' => 60,  'minimum_stock' => 10, 'type' => 'physical'],
            ['product_code' => 'PRD00009', 'name' => 'Earphone Bluetooth TWS',    'category_code' => 'CAT0002',  'supplier_code' => 'SUP0002', 'purchase_price' => 35000,   'selling_price' => 75000,   'stock' => 20,  'minimum_stock' => 5,  'type' => 'physical'],
            ['product_code' => 'PRD00010', 'name' => 'LCD Samsung A15',           'category_code' => 'CAT0003',  'supplier_code' => 'SUP0003', 'purchase_price' => 150000,  'selling_price' => 250000,  'stock' => 5,   'minimum_stock' => 2,  'type' => 'physical'],
            ['product_code' => 'PRD00011', 'name' => 'Baterai Xiaomi Redmi Note', 'category_code' => 'CAT0003',  'supplier_code' => 'SUP0003', 'purchase_price' => 80000,   'selling_price' => 150000,  'stock' => 8,   'minimum_stock' => 2,  'type' => 'physical'],
            ['product_code' => 'PRD00012', 'name' => 'Connector Charger Type-C',  'category_code' => 'CAT0003',  'supplier_code' => 'SUP0003', 'purchase_price' => 15000,   'selling_price' => 50000,   'stock' => 2,   'minimum_stock' => 3,  'type' => 'physical'],
        ];

        foreach ($productDefs as $p) {
            $p['status'] = 'active';
            Product::firstOrCreate(['product_code' => $p['product_code']], $p);
        }

        // Digital products
        $digitalDefs = [
            ['product_code' => 'DIG00001', 'name' => 'Pulsa Telkomsel 50K', 'category_code' => 'CAT0004', 'purchase_price' => 48000, 'selling_price' => 51000,  'stock' => 999, 'minimum_stock' => 1, 'type' => 'digital', 'status' => 'active'],
            ['product_code' => 'DIG00002', 'name' => 'Pulsa XL 25K',        'category_code' => 'CAT0004', 'purchase_price' => 23500, 'selling_price' => 26000,  'stock' => 999, 'minimum_stock' => 1, 'type' => 'digital', 'status' => 'active'],
            ['product_code' => 'DIG00003', 'name' => 'Paket Data 10GB',     'category_code' => 'CAT0004', 'purchase_price' => 55000, 'selling_price' => 65000,  'stock' => 999, 'minimum_stock' => 1, 'type' => 'digital', 'status' => 'active'],
            ['product_code' => 'DIG00004', 'name' => 'Top Up DANA 100K',    'category_code' => 'CAT0004', 'purchase_price' => 99000, 'selling_price' => 102000, 'stock' => 999, 'minimum_stock' => 1, 'type' => 'digital', 'status' => 'active'],
        ];
        foreach ($digitalDefs as $dp) {
            Product::firstOrCreate(['product_code' => $dp['product_code']], $dp);
        }
    }
}
