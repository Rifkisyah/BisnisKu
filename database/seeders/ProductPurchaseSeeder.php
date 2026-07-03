<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $supplier = Supplier::first();
        $product = Product::first();
        $user = User::first();
        if (!$supplier || !$product || !$user) return;

        for ($i = 1; $i <= 20; $i++) {
            $code = 'PPR' . now()->format('Ymd') . str_pad($i, 4, '0', STR_PAD_LEFT);
            $purchase = ProductPurchase::firstOrCreate(['product_purchase_code' => $code], [
                'source' => 'offline',
                'supplier_code' => $supplier->supplier_code,
                'created_by' => $user->id,
                'purchase_date' => $faker->dateTimeBetween('-1 month', 'now'),
                'estimated_arrival_date' => $faker->dateTimeBetween('now', '+1 month'),
                'total' => $faker->numberBetween(10, 100) * 10000,
                'status' => $faker->randomElement(['draft', 'ordered', 'received']),
                'notes' => $faker->sentence(),
            ]);
            ProductPurchaseItem::firstOrCreate(['product_purchase_code' => $code, 'product_code' => $product->product_code], [
                'quantity' => $faker->numberBetween(5, 20),
                'purchase_price' => $product->purchase_price,
                'subtotal' => $product->purchase_price * 5,
            ]);
        }
    }
}
