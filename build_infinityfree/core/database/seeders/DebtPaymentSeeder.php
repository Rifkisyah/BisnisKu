<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Database\Seeder;

class DebtPaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Buat pembayaran untuk semua hutang yang memiliki paid_amount > 0
        // Mencakup: partial (34 data) + paid (16 data) = total 50 debt payments
        $debts = Debt::where('paid_amount', '>', 0)->get();
        if ($debts->isEmpty()) return;

        $methods = ['cash', 'cash', 'qris', 'cash', 'cash', 'qris', 'qris', 'cash'];

        foreach ($debts as $i => $debt) {
            $method = $methods[$i % count($methods)];

            // Untuk hutang yang paid (fully paid), bisa ada 1 atau 2 cicilan
            if ($debt->status === 'paid' && $debt->paid_amount >= 150000 && ($i % 3 === 0)) {
                // Cicilan pertama
                $firstPayment = (int) round($debt->paid_amount * 0.6);
                DebtPayment::firstOrCreate(
                    [
                        'debt_code' => $debt->debt_code,
                        'amount'    => $firstPayment,
                    ],
                    [
                        'notes'          => 'Cicilan pertama (' . strtoupper($method) . ')',
                        'payment_method' => $method,
                        'payment_date'   => $debt->debt_date->copy()->addDays(2),
                    ]
                );
                // Cicilan pelunasan
                $secondPayment = $debt->paid_amount - $firstPayment;
                $method2 = $methods[($i + 2) % count($methods)];
                DebtPayment::firstOrCreate(
                    [
                        'debt_code' => $debt->debt_code,
                        'amount'    => $secondPayment,
                    ],
                    [
                        'notes'          => 'Pelunasan hutang (' . strtoupper($method2) . ')',
                        'payment_method' => $method2,
                        'payment_date'   => $debt->debt_date->copy()->addDays(5),
                    ]
                );
            } else {
                // Single payment
                DebtPayment::firstOrCreate(
                    [
                        'debt_code' => $debt->debt_code,
                    ],
                    [
                        'amount'         => $debt->paid_amount,
                        'notes'          => 'Pembayaran cicilan / pelunasan (' . strtoupper($method) . ')',
                        'payment_method' => $method,
                        'payment_date'   => $debt->debt_date->copy()->addDays(2),
                    ]
                );
            }

            $debt->updateStatus();
        }
    }
}
