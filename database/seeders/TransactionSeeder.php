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

        for ($i = 1; $i <= 20; $i++) {
            $date = $faker->dateTimeBetween('-2 months', 'now');
            $txCode = 'TRX' . $date->format('Ymd') . str_pad($i, 4, '0', STR_PAD_LEFT);
            
            $itemsCount = $faker->numberBetween(1, 3);
            $total = 0;
            $items = [];
            
            for ($j = 0; $j < $itemsCount; $j++) {
                $product = $products->random();
                $qty = $faker->numberBetween(1, 2);
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
                'transaction_date' => $date,
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

        app()->forgetInstance('current_store');
    }
}
