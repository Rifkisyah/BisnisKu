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
        $suppliers = Supplier::all();
        $products = Product::all();
        $user = User::whereHas('role', function($q) { $q->whereIn('name', ['owner', 'gudang']); })->first() ?? User::first();
        if ($suppliers->isEmpty() || $products->isEmpty() || !$user) return;

        $startDate = now()->subDays(90);
        $purchaseCounter = 1;

        for ($d = 0; $d <= 90; $d++) {
            $currentDate = (clone $startDate)->addDays($d);
            $dailyPurchaseCount = $faker->numberBetween(2, 3); // Increase to 2-3 purchases per day
            
            for ($i = 0; $i < $dailyPurchaseCount; $i++) {
                $purchaseTime = (clone $currentDate)->setTime(rand(8, 17), rand(0, 59), rand(0, 59));
                $code = 'PPR' . $purchaseTime->format('Ymd') . str_pad($purchaseCounter++, 4, '0', STR_PAD_LEFT);
                $supplier = $suppliers->random();
                
                $purchase = ProductPurchase::firstOrCreate(['product_purchase_code' => $code], [
                    'store_id' => $store?->id,
                    'created_by' => $user->id,
                    'purchase_date' => $purchaseTime,
                    'estimated_arrival_date' => (clone $purchaseTime)->modify('+' . rand(1, 14) . ' days'),
                    'total' => 0, // will calculate later
                    'status' => $faker->randomElement(['draft', 'ordered', 'received']),
                    'notes' => $faker->sentence(),
                ]);

                $itemsCount = $faker->numberBetween(1, 5);
                $total = 0;
                
                for ($j = 0; $j < $itemsCount; $j++) {
                    $product = $products->random();
                    $qty = $faker->numberBetween(1, 5); // Keep quantity low
                    $subtotal = $product->purchase_price * $qty;
                    $total += $subtotal;
                    
                    ProductPurchaseItem::firstOrCreate([
                        'product_purchase_code' => $code, 
                        'product_code' => $product->product_code
                    ], [
                        'source' => 'offline',
                        'supplier_code' => $supplier->supplier_code,
                        'is_resolved' => true,
                        'quantity' => $qty,
                        'quantity_received' => $purchase->status === 'received' ? $qty : 0,
                        'quantity_rejected' => 0,
                        'purchase_price' => $product->purchase_price,
                        'subtotal' => $subtotal,
                    ]);
                }
                
                $purchase->update(['total' => $total]);
            }
        }
        
        app()->forgetInstance('current_store');
    }
}
