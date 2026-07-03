<?php

namespace Database\Seeders;

use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use App\Models\Store;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ServiceRepairSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $teknisiUser = User::whereHas('role', function($q) { $q->where('name', 'teknisi'); })->where('store_id', $store?->id)->first();
        if (!$teknisiUser) return;

        $brands = ['Samsung', 'iPhone', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'Infinix', 'Poco'];
        $series = ['Galaxy S22', '11 Pro', 'Redmi Note 10', 'A5s', 'Y20', 'C2', 'Hot 10', 'X3 Pro', 'Galaxy A54', '13 Pro Max'];
        $complaints = [
            'Layar retak/pecah', 'Baterai cepat habis/drop', 'Mati total', 'Tidak bisa dicas', 
            'Kamera buram', 'Lupa pola/password', 'Suara speaker pecah', 'Sinyal hilang', 
            'Tombol power keras/rusak', 'Bootloop/Stuck logo'
        ];

        for ($i = 1; $i <= 20; $i++) {
            $repairCode = 'SRV' . now()->format('Ymd') . str_pad($i, 4, '0', STR_PAD_LEFT);
            $fee = $faker->numberBetween(10, 50) * 10000;
            $status = $faker->randomElement(['diagnosing', 'repairing', 'done', 'cancelled']);
            ServiceRepair::firstOrCreate(['repair_code' => $repairCode], [
                'technician_id' => $teknisiUser->id,
                'customer_name' => $faker->name(),
                'customer_phone' => $faker->numerify('08##########'),
                'service_fee' => $fee,
                'component_cost' => 0,
                'total_cost' => $fee,
                'payment_method' => 'cash',
                'down_payment' => 0,
                'status' => $status,
                'start_date' => $faker->dateTimeBetween('-1 month', 'now'),
                'completion_date' => $status === 'done' ? now() : null,
            ]);
            $brand = $faker->randomElement($brands);
            ServiceRepairItem::firstOrCreate([
                'repair_code' => $repairCode,
                'name' => $brand . ' ' . $faker->randomElement($series),
            ], [
                'brand' => $brand,
                'series' => $faker->randomElement($series),
                'complaint' => $faker->randomElement($complaints),
                'quantity' => 1,
                'service_fee' => $fee,
                'subtotal' => $fee,
            ]);
        }
    }
}
