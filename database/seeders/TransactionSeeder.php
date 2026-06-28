<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\User;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $kasirUser = User::where('email', 'kasir@armalcellular.test')->first();
        if (!$kasirUser) return;
        
        $products = Product::where('type', 'physical')->get();
        if ($products->isEmpty()) return;

        for ($i = 0; $i < 20; $i++) {
            $date    = now()->subDays(rand(0, 60));
            $product = $products->random();
            $qty     = rand(1, 3);
            $subtotal = $product->selling_price * $qty;
            $txCode = 'TRX' . $date->format('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $trx = Transaction::firstOrCreate(['transaction_code' => $txCode], [
                'cashier_id'       => $kasirUser->id,
                'transaction_date' => $date,
                'subtotal'         => $subtotal,
                'discount'         => 0,
                'total'            => $subtotal,
                'payment_method'   => ['cash', 'transfer', 'qris'][rand(0, 2)],
                'amount_paid'      => $subtotal,
                'change_amount'    => 0,
                'status'           => 'completed',
            ]);
            TransactionItem::firstOrCreate([
                'transaction_code' => $txCode,
                'product_code'     => $product->product_code,
            ], [
                'quantity'         => $qty,
                'unit_price'       => $product->selling_price,
                'discount_product' => 0,
                'subtotal'         => $subtotal,
            ]);
        }
    }
}
