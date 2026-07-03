<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DebtPayment;
use App\Models\Debt;
use Faker\Factory as Faker;

class DebtPaymentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $debts = Debt::where('paid_amount', '>', 0)->get();
        if ($debts->isEmpty()) return;
        
        foreach ($debts as $debt) {
            DebtPayment::firstOrCreate(['debt_code' => $debt->debt_code], [
                'amount' => $debt->paid_amount,
                'payment_method' => 'cash',
                'payment_date' => now(),
                'notes' => 'Pembayaran awal',
            ]);
        }
    }
}
