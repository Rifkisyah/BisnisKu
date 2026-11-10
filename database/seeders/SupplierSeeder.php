<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $suppliers = [
            [
                'code' => 'SUP0001',
                'name' => 'Grosir Aksesoris Roxy Mas',
                'phone_number' => '81234000101',
                'email' => 'roxy.grosir@armalcellular.test',
                'address' => 'ITC Roxy Mas Lantai 2, Jakarta Pusat',
            ],
            [
                'code' => 'SUP0002',
                'name' => 'Distributor Voucher Nusantara',
                'phone_number' => '81234000102',
                'email' => 'voucher.nusantara@armalcellular.test',
                'address' => 'Jl. Kebayoran Lama No. 45, Jakarta Selatan',
            ],
            [
                'code' => 'SUP0003',
                'name' => 'Mitra Sparepart & LCD Jakarta',
                'phone_number' => '81234000103',
                'email' => 'sparepart.mitra@armalcellular.test',
                'address' => 'Pusat Sparepart Roxy Lantai Dasar, Jakarta Pusat',
            ],
            [
                'code' => 'SUP0004',
                'name' => 'Agen Perdana Telkomsel & Indosat',
                'phone_number' => '81234000104',
                'email' => 'agen.perdana@armalcellular.test',
                'address' => 'Jl. Matraman Raya No. 12, Jakarta Timur',
            ],
            [
                'code' => 'SUP0005',
                'name' => 'Vivan & Robot Authorized Dealer',
                'phone_number' => '81234000105',
                'email' => 'vivan.robot@armalcellular.test',
                'address' => 'Mangga Dua Mall Lantai 3, Jakarta Barat',
            ],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(
                ['supplier_code' => $s['code']],
                [
                    'store_id'     => $store?->id,
                    'name'         => $s['name'],
                    'phone_prefix' => '+62',
                    'phone_number' => $s['phone_number'],
                    'email'        => $s['email'],
                    'address'      => $s['address'],
                    'is_active'    => true,
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}
