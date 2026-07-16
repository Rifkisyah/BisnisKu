<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\Store;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class DebtSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $startDate = now()->subDays(90);
        $debtCounter = 1;

        for ($d = 0; $d <= 90; $d++) {
            $currentDate = (clone $startDate)->addDays($d);
            $dailyDebtCount = $faker->numberBetween(2, 4); // Increase to 2-4 debts per day
            
            for ($i = 0; $i < $dailyDebtCount; $i++) {
                $debtTime = (clone $currentDate)->setTime(rand(9, 20), rand(0, 59), rand(0, 59));
                $amount = $faker->numberBetween(5, 50) * 10000; // Keep debt under 500k to avoid huge totals
                $paid = $faker->randomElement([0, $amount / 2, $amount]);
                $debtCode = 'DBT' . str_pad($debtCounter++, 5, '0', STR_PAD_LEFT);
                Debt::firstOrCreate(['debt_code' => $debtCode], [
                    'transaction_code' => null,
                    'debtor_name' => $faker->name(),
                    'debtor_contact' => $faker->numerify('08##########'),
                    'debtor_address' => $faker->address(),
                    'total_amount' => $amount,
                    'paid_amount' => $paid,
                    'remaining_amount' => $amount - $paid,
                    'debt_date' => $debtTime,
                    'due_date' => (clone $debtTime)->modify('+' . rand(7, 30) . ' days'),
                    'status' => $paid >= $amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                ]);
            }
        }
    }
}
