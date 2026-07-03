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

        app()->forgetInstance('current_store');
    }
}

