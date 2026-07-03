<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Supplier;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $suppliers = [
            'TAM Official', 'Xiaomi Authorized Distributor', 'Vivan Official Store', 'Robot Accessories', 'Indosat Ooredoo Dealer',
            'Telkomsel Branch', 'XL Axiata Center', 'Grosir Roxy Mas', 'Grosir Sparepart Cempaka Mas', 'Oppo Indonesia',
            'Vivo Official Distributor', 'Realme Center', 'Baseus Official', 'JBL Authorized', 'Samsung Electronics Indonesia',
            'Smartfren Distributor', 'Tri Hutchison Dealer', 'ITC Roxy Supplier', 'Glodok Elektronik', 'Pusat Baterai Jakarta'
        ];

        foreach ($suppliers as $i => $name) {
            Supplier::firstOrCreate(
                ['supplier_code' => 'SUP' . str_pad($i + 1, 4, '0', STR_PAD_LEFT)],
                [
                    'store_id'     => $store?->id,
                    'name'         => $name,
                    'phone_prefix' => '+62',
                    'phone_number' => $faker->numerify('8##########'),
                    'email'        => $faker->unique()->safeEmail(),
                    'address'      => $faker->address(),
                    'is_active'    => true,
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}

