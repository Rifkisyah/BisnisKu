<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $faker = Faker::create('id_ID');
        $products = Product::all();
        $user = User::first();
        if ($products->isEmpty() || !$user) return;
        
        for ($i = 1; $i <= 100; $i++) {
            $product = $products->random();
            StockMovement::create([
                'store_id' => $store?->id,
                'product_code' => $product->product_code,
                'created_by' => $user->id,
                'type' => $faker->randomElement(['in', 'out']),
                'total_stock' => $faker->numberBetween(1, 10),
                'previous_stock' => 0,
                'current_stock' => 10,
                'movement_date' => $faker->dateTimeBetween('-3 months', 'now'),
                'reference_type' => 'manual',
                'reference_code' => 'REF' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'notes' => $faker->sentence(),
            ]);
        }
    }
}
