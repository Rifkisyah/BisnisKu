<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use App\Models\Store;
use Illuminate\Database\Seeder;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        PaymentSetting::updateOrCreate(
            ['store_id' => $store?->id],
            [
                'qris_mode' => 'manual',
                'is_qris_active' => true,
                'qris_provider' => 'DANA',
                'merchant_id' => 'DANA-ARMAL-12345',
                'bank_name' => 'BCA',
                'bank_account_number' => '1234567890',
                'bank_account_name' => 'Armal Cellular',
            ]
        );

        app()->forgetInstance('current_store');
    }
}
