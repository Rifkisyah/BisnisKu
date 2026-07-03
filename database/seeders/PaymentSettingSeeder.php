<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentSetting;

class PaymentSettingSeeder extends Seeder
{
    public function run(): void
    {
        PaymentSetting::firstOrCreate(['id' => 1], [
            'qris_mode' => 'manual',
            'is_qris_active' => true,
        ]);
    }
}
