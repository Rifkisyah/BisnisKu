<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Product;

#[Signature('app:check-db')]
#[Description('Check database state (users, products, status)')]
class CheckDb extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("--- USERS ---");
        $users = User::all();
        foreach($users as $u) {
            $this->line("User: {$u->username} | Role: {$u->role->name} | StoreID: {$u->store_id}");
        }

        $this->info("\n--- PRODUCTS ---");
        $products = Product::withoutGlobalScope('store')->with('category')->get();
        foreach($products as $p) {
            $catType = $p->category ? $p->category->type : 'none';
            $catSlug = $p->category ? $p->category->slug : 'none';
            $this->line("Product: {$p->name} | StoreID: {$p->store_id} | Stock: {$p->stock} | Type: {$p->type} | CatType: {$catType} | CatSlug: {$catSlug}");
        }

        $this->info("\n--- STORE 3 PRODUCT STATUS ---");
        foreach(Product::withoutGlobalScope('store')->where('store_id', 3)->get() as $p) {
            $this->line($p->name . ' (Status: ' . $p->status . ")");
        }
    }
}
