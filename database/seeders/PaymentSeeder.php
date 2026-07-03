<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Store;
use App\Models\Transaction;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $transactions = Transaction::all();
        if ($transactions->isEmpty()) return;

        foreach ($transactions as $i => $transaction) {
            $code = 'PAY-' . now()->format('Ymd') . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
            $method = $transaction->payment_method;
            
            $provider = null;
            if ($method === 'qris') {
                $provider = 'DANA';
            } elseif ($method === 'transfer') {
                $provider = $faker->randomElement(['BCA', 'Mandiri', 'BNI', 'BRI']);
            } else {
                $provider = 'CASH';
            }

            Payment::firstOrCreate(['payment_code' => $code], [
                'transaction_code' => $transaction->transaction_code,
                'payment_method' => $method,
                'qris_mode' => $method === 'qris' ? 'dynamic' : null,
                'provider' => $provider,
                'amount' => $transaction->total,
                'status' => 'paid',
                'external_order_id' => $method === 'qris' ? 'DANA-' . $faker->randomNumber(8) : null,
                'reference_number' => 'REF' . $faker->randomNumber(5),
                'paid_at' => $transaction->transaction_date,
                'confirmed_by' => $transaction->cashier_id,
            ]);
        }
    }
}
