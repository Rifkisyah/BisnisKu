<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Store;
use App\Models\Transaction;

use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }


        $transactions = Transaction::all();
        if ($transactions->isEmpty()) return;

        foreach ($transactions as $i => $transaction) {
            $method = $transaction->payment_method;
            if ($method === 'debt') continue;

            $code = 'PAY-' . $transaction->transaction_date->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);

            $provider = 'CASH';
            if ($method === 'qris') {
                $provider = 'DANA';
            }

            Payment::firstOrCreate(
                ['payment_code' => $code],
                [
                    'transaction_code'  => $transaction->transaction_code,
                    'payment_method'    => $method,
                    'qris_mode'         => $method === 'qris' ? 'manual' : null,
                    'provider'          => $provider,
                    'amount'            => $transaction->total,
                    'status'            => 'paid',
                    'external_order_id' => $method === 'qris' ? 'DANA-' . rand(10000000, 99999999) : null,
                    'reference_number'  => 'REF' . rand(10000, 99999),
                    'paid_at'           => $transaction->transaction_date,
                    'confirmed_by'      => $transaction->cashier_id,
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}
