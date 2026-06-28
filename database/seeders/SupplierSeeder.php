<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        Supplier::firstOrCreate(['supplier_code' => 'SUP0001'], ['name' => 'PT Distributor Gadget', 'phone_prefix' => '+62', 'phone_number' => '211234567', 'email' => 'info@distgadget.com',  'address' => 'Jakarta',   'is_active' => true]);
        Supplier::firstOrCreate(['supplier_code' => 'SUP0002'], ['name' => 'CV Aksesoris Murah',    'phone_prefix' => '+62', 'phone_number' => '217654321', 'email' => 'sales@accmurah.com',   'address' => 'Bandung',   'is_active' => true]);
        Supplier::firstOrCreate(['supplier_code' => 'SUP0003'], ['name' => 'Toko Sparepart Online', 'phone_prefix' => '+62', 'phone_number' => '8123456789',                                     'address' => 'Surabaya',  'is_active' => true]);
    }
}
