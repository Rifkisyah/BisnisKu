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
        for ($i = 1; $i <= 20; $i++) {
            $amount = $faker->numberBetween(10, 100) * 10000;
            $paid = $faker->randomElement([0, $amount / 2, $amount]);
            $debtCode = 'DBT' . str_pad($i, 5, '0', STR_PAD_LEFT);
            Debt::firstOrCreate(['debt_code' => $debtCode], [
                'transaction_code' => null,
                'debtor_name' => $faker->name(),
                'debtor_contact' => $faker->numerify('08##########'),
                'debtor_address' => $faker->address(),
                'total_amount' => $amount,
                'paid_amount' => $paid,
                'remaining_amount' => $amount - $paid,
                'debt_date' => $faker->dateTimeBetween('-2 months', 'now'),
                'due_date' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => $paid >= $amount ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
            ]);
        }
    }
}
