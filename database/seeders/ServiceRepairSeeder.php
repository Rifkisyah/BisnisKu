<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use App\Models\User;

class ServiceRepairSeeder extends Seeder
{
    public function run(): void
    {
        $teknisiUser = User::where('email', 'teknisi@armalcellular.test')->first();
        if (!$teknisiUser) return;

        $repairDefs = [
            ['customer_name' => 'Budi Santoso', 'customer_phone' => '08112233445', 'service_fee' => 150000, 'status' => 'repairing', 'item_name' => 'Samsung Galaxy A15',   'brand' => 'Samsung', 'series' => 'Galaxy A15',   'complaint' => 'LCD retak, touchscreen tidak responsif'],
            ['customer_name' => 'Siti Rahayu',  'customer_phone' => '08556677889', 'service_fee' => 100000, 'status' => 'done',   'item_name' => 'Xiaomi Redmi Note 12', 'brand' => 'Xiaomi',  'series' => 'Redmi Note 12', 'complaint' => 'Baterai cepat habis, tidak bisa di-charge'],
            ['customer_name' => 'Ahmad Fauzi',  'customer_phone' => '08199887766', 'service_fee' => 75000,  'status' => 'diagnosing',    'item_name' => 'OPPO A18',             'brand' => 'OPPO',    'series' => 'A18',           'complaint' => 'Tombol power tidak berfungsi'],
        ];
        
        foreach ($repairDefs as $idx => $r) {
            $repairCode = 'SRV' . now()->format('Ymd') . str_pad($idx + 1, 4, '0', STR_PAD_LEFT);
            $repair = ServiceRepair::firstOrCreate(['repair_code' => $repairCode], [
                'technician_id'    => $teknisiUser->id,
                'customer_name'    => $r['customer_name'],
                'customer_phone'   => $r['customer_phone'],
                'service_fee'      => $r['service_fee'],
                'component_cost'   => 0,
                'total_cost'       => $r['service_fee'],
                'payment_method'   => 'cash',
                'down_payment'     => 0,
                'status'           => $r['status'],
                'start_date'       => now()->subDays(rand(0, 10)),
                'completion_date'  => $r['status'] === 'done' ? now()->subDays(rand(0, 3)) : null,
            ]);
            ServiceRepairItem::firstOrCreate([
                'repair_code'    => $repairCode,
                'name'           => $r['item_name'],
            ], [
                'brand'          => $r['brand'],
                'series'         => $r['series'],
                'complaint'      => $r['complaint'],
                'quantity'       => 1,
                'service_fee'    => $r['service_fee'],
                'subtotal'       => 0,
            ]);
        }
    }
}
