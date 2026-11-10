<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\Store;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $settings = [
            'store_name' => 'Armal Cellular',
            'store_address' => 'Jl. Pahlawan No. 123, Jakarta Selatan',
            'store_phone' => '081234567890',
            'store_email' => 'info@armalcellular.com',
            'receipt_footer' => 'Terima kasih telah berbelanja di Armal Cellular. Barang yang sudah dibeli tidak dapat dikembalikan.',
            'tax_percentage' => '0',
            'default_currency' => 'IDR',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        app()->forgetInstance('current_store');
    }
}
