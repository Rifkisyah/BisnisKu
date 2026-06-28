<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::firstOrCreate(['category_code' => 'CAT0001'], ['name' => 'Handphone',     'slug' => 'handphone',     'type' => 'product',  'is_active' => true]);
        Category::firstOrCreate(['category_code' => 'CAT0002'], ['name' => 'Aksesoris',     'slug' => 'aksesoris',     'type' => 'product',  'is_active' => true]);
        Category::firstOrCreate(['category_code' => 'CAT0003'], ['name' => 'Sparepart',     'slug' => 'sparepart',     'type' => 'product',  'is_active' => true]);
        Category::firstOrCreate(['category_code' => 'CAT0004'], ['name' => 'Layanan Digital','slug' => 'layanan-digital','type' => 'product',  'is_active' => true]);
        Category::firstOrCreate(['category_code' => 'CAT0005'], ['name' => 'Jasa Servis',   'slug' => 'jasa-servis',   'type' => 'service',  'is_active' => true]);
    }
}
