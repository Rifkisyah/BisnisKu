<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
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
            Setting::firstOrCreate(['key' => $key], [
                'value' => $value,
            ]);
        }
    }
}
