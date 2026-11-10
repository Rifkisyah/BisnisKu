<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Store;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $categories = [
            ['code' => 'CAT0001', 'name' => 'Voucher Internet & Kuota', 'type' => 'product'],
            ['code' => 'CAT0002', 'name' => 'Pulsa Elektrik & Token', 'type' => 'product'],
            ['code' => 'CAT0003', 'name' => 'Kartu Perdana & Nomor Cantik', 'type' => 'product'],
            ['code' => 'CAT0004', 'name' => 'Charger & Kabel Data', 'type' => 'product'],
            ['code' => 'CAT0005', 'name' => 'Aksesoris & Tempered Glass', 'type' => 'product'],
            ['code' => 'CAT0006', 'name' => 'Audio, Headset & TWS', 'type' => 'product'],
            ['code' => 'CAT0007', 'name' => 'Powerbank & Baterai HP', 'type' => 'product'],
            ['code' => 'CAT0008', 'name' => 'Sparepart LCD & Konektor', 'type' => 'product'],
            ['code' => 'CAT0009', 'name' => 'Servis Hardware HP', 'type' => 'service'],
            ['code' => 'CAT0010', 'name' => 'Servis Software & Flashing', 'type' => 'service'],
        ];

        foreach ($categories as $c) {
            Category::firstOrCreate(
                ['category_code' => $c['code']],
                [
                    'store_id'  => $store?->id,
                    'name'      => $c['name'],
                    'slug'      => Str::slug($c['name']),
                    'type'      => $c['type'],
                    'is_active' => true,
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}
