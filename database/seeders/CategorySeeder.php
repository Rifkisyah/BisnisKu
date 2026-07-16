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
        // Seed categories for Toko 1 (Armal Cellular)
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $categories = [
            ['name' => 'Smartphone Baru', 'type' => 'product'],
            ['name' => 'Smartphone Bekas', 'type' => 'product'],
            ['name' => 'Feature Phone', 'type' => 'product'],
            ['name' => 'Tablet', 'type' => 'product'],
            ['name' => 'Casing & Cover', 'type' => 'product'],
            ['name' => 'Screen Protector', 'type' => 'product'],
            ['name' => 'Charger & Kabel', 'type' => 'product'],
            ['name' => 'Earphone & TWS', 'type' => 'product'],
            ['name' => 'Powerbank', 'type' => 'product'],
            ['name' => 'Baterai', 'type' => 'product'],
            ['name' => 'LCD & Touchscreen', 'type' => 'product'],
            ['name' => 'Kamera & Lensa Part', 'type' => 'product'],
            ['name' => 'Fleksibel & Konektor', 'type' => 'product'],
            ['name' => 'Voucher Internet', 'type' => 'product'],
            ['name' => 'Pulsa Elektrik', 'type' => 'product'],
            ['name' => 'Perdana & Nomor Cantik', 'type' => 'product'],
            ['name' => 'Aksesoris Lainnya', 'type' => 'product'],
            ['name' => 'Servis Hardware', 'type' => 'service'],
            ['name' => 'Servis Software', 'type' => 'service'],
            ['name' => 'Pemasangan Aksesoris', 'type' => 'service'],
        ];

        foreach ($categories as $i => $c) {
            Category::firstOrCreate(
                ['category_code' => 'CAT' . str_pad($i+1, 4, '0', STR_PAD_LEFT)],
                ['store_id' => $store?->id, 'name' => $c['name'], 'slug' => Str::slug($c['name']), 'type' => $c['type'], 'is_active' => true]
            );
        }

        $extraCategories = [
            'Speaker Bluetooth', 'Headphone Wireless', 'Smartwatch', 'Fitness Band', 'Kabel OTG', 
            'Card Reader', 'Flashdisk', 'MicroSD', 'Tripod & Monopod', 'Ring Light', 
            'Gimbal Stabilizer', 'Casing Waterproof', 'Pop Socket', 'Lanyard HP', 'Holder Motor',
            'Holder Mobil', 'Kabel HDMI', 'Converter Audio', 'Pelindung Kabel', 'Strap Smartwatch',
            'Baterai Kancing', 'Stylus Pen', 'Kipas HP', 'Microphone Clip-on', 'Game Controller HP',
            'Kabel LAN', 'Router Wi-Fi', 'Modem Mifi', 'Antena Penguat Sinyal', 'SIM Card Ejector',
            'Cairan Pembersih Layar', 'Lap Microfiber', 'Pelindung Lensa Kamera', 'Tempered Glass Anti Spy', 'Hydrogel Screen',
            'Skin HP', 'Casing Akrilik', 'Silikon HP', 'Hardcase Matte', 'Leather Case',
            'Lensa Tambahan HP', 'Kabel Aux', 'Adaptor Fast Charging', 'Charger Mobil', 'Wireless Charger',
            'Kabel Data Micro USB', 'Kabel Data Lightning', 'TWS Gaming', 'Earphone Kabel', 'Headset Gaming',
            'Baterai Tanam', 'Baterai Double Power', 'LCD Original', 'OLED Display', 'Touchscreen Pengganti',
            'Kamera Depan', 'Kamera Belakang', 'Speaker Internal', 'Mic Internal', 'Konektor Papan Bawah',
            'Fleksibel Power', 'Fleksibel Volume', 'IC Power', 'IC eMMC', 'IC Audio',
            'Timah Solder', 'Flux Solder', 'Blower Hot Air', 'Avometer', 'Lem LCD',
            'Pinset Servis', 'Obeng Set', 'Baut HP', 'Solder Iron', 'Kabel Jumper',
            'Voucher Indosat', 'Voucher XL', 'Voucher Tri', 'Voucher Smartfren', 'Perdana Telkomsel'
        ];

        // Ensure we loop exactly enough to add real names, we can take up to 80 items
        for ($i = 21; $i <= 100; $i++) {
            $idx = $i - 21;
            // Fallback to random if we run out of the 80 predefined names (shouldn't happen since array has 80 items)
            $name = $extraCategories[$idx] ?? ('Aksesoris Lain ' . $idx); 
            
            Category::firstOrCreate(
                ['category_code' => 'CAT' . str_pad($i, 4, '0', STR_PAD_LEFT)],
                [
                    'store_id' => $store?->id, 
                    'name' => $name, 
                    'slug' => Str::slug($name) . '-' . $i, 
                    'type' => (str_contains(strtolower($name), 'servis') || str_contains(strtolower($name), 'pemasangan')) ? 'service' : 'product', 
                    'is_active' => true
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}

