<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductAnalytic;
use App\Models\Store;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ProductAnalyticSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $products = Product::take(20)->get();
        if ($products->isEmpty()) return;

        foreach ($products as $product) {
            ProductAnalytic::firstOrCreate(['product_code' => $product->product_code], [
                'total_qty_sold' => $faker->numberBetween(10, 100),
                'transaction_frequency' => $faker->numberBetween(5, 50),
                'remaining_stock' => $product->stock,
                'cluster_label' => $faker->randomElement(['Tinggi', 'Sedang', 'Rendah']),
                'sma_value' => $faker->randomFloat(2, 5, 20),
                'predicted_demand' => $faker->numberBetween(10, 50),
                'restock_recommendation' => $faker->numberBetween(10, 50),
                'analysis_date' => now(),
            ]);
        }
    }
}
