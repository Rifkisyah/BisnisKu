<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $kasirUser = User::whereHas('role', function($q) { $q->where('name', 'kasir'); })->where('store_id', $store?->id)->first();
        $products = Product::where('type', 'physical')->get();
        if (!$kasirUser || $products->isEmpty()) return;

        // We want to create transactions over the last 90 days with varying daily frequencies
        // for realistic K-Means and SMA testing.
        
        $highDemandProducts = $products->take(4); // First 4 products are top sellers
        $mediumDemandProducts = $products->slice(4, 6); // Next 6 are medium
        $lowDemandProducts = $products->slice(10); // Rest are low demand

        $startDate = now()->subDays(90);
        $txCounter = 1;

        for ($d = 0; $d <= 90; $d++) {
            $currentDate = (clone $startDate)->addDays($d);
            
            // Varying frequency: between 1 and 5 transactions per day to keep total revenue in tens of millions
            $dailyTxCount = $faker->numberBetween(1, 5);
            
            for ($i = 0; $i < $dailyTxCount; $i++) {
                // Random time within the day
                $txTime = (clone $currentDate)->setTime(rand(8, 22), rand(0, 59), rand(0, 59));
                $txCode = 'TRX' . $txTime->format('Ymd') . str_pad($txCounter++, 4, '0', STR_PAD_LEFT);
                
                $itemsCount = $faker->numberBetween(1, 2);
                $total = 0;
                $items = [];
                
                for ($j = 0; $j < $itemsCount; $j++) {
                    $chance = $faker->numberBetween(1, 100);
                    if ($chance <= 60) {
                        $product = $highDemandProducts->random();
                    } elseif ($chance <= 90) {
                        $product = $mediumDemandProducts->random();
                    } else {
                        $product = $lowDemandProducts->random();
                    }

                    $qty = 1; // Keep quantity low
                    if ($chance <= 60) $qty = $faker->numberBetween(1, 2);

                    $subtotal = $product->selling_price * $qty;
                    $total += $subtotal;
                    $items[] = [
                        'product_code' => $product->product_code,
                        'quantity' => $qty,
                        'unit_price' => $product->selling_price,
                        'subtotal' => $subtotal,
                    ];
                }

                Transaction::firstOrCreate(['transaction_code' => $txCode], [
                    'store_id'        => $store?->id,
                    'cashier_id'      => $kasirUser->id,
                    'transaction_date' => $txTime,
                    'subtotal'        => $total,
                    'discount'        => 0,
                    'total'           => $total,
                    'payment_method'  => $faker->randomElement(['cash', 'transfer', 'qris']),
                    'amount_paid'     => $total,
                    'change_amount'   => 0,
                    'status'          => 'paid',
                ]);

                foreach ($items as $item) {
                    TransactionItem::firstOrCreate([
                        'transaction_code' => $txCode,
                        'product_code' => $item['product_code'],
                    ], [
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount_product' => 0,
                        'subtotal' => $item['subtotal'],
                    ]);
                }
            }
        }

        app()->forgetInstance('current_store');
    }
}
