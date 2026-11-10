<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StoreSeeder extends Seeder
{
    public function run(): void
    {
        $ownerRole  = Role::where('name', 'owner')->firstOrFail();
        $kasirRole  = Role::where('name', 'kasir')->firstOrFail();
        $teknisiRole = Role::where('name', 'teknisi')->firstOrFail();
        $gudangRole = Role::where('name', 'gudang')->firstOrFail();

        // ─── UMKM Mikro Konter: Armal Cellular ─────────────────────────────
        $store = Store::firstOrCreate(
            ['slug' => 'armal-cellular'],
            [
                'name'            => 'Armal Cellular',
                'address'         => 'Jl. Pahlawan No. 123, Jakarta Selatan',
                'phone'           => '081234567890',
                'email'           => 'info@armalcellular.com',
                'description'     => 'Toko Handphone, Pulsa, Aksesoris & Servis HP UMKM Mikro Terpercaya.',
                'is_active'       => true,
                'catalog_enabled' => true,
            ]
        );

        $owner = User::firstOrCreate(
            ['email' => 'owner@armalcellular.test'],
            [
                'username' => 'Owner Armal',
                'password' => Hash::make('password'),
                'role_id'  => $ownerRole->id,
                'store_id' => $store->id,
                'contact'  => '081234567890',
                'status'   => 'active',
            ]
        );

        // Link owner to store
        $store->update(['owner_id' => $owner->id]);

        // Staf Toko 1
        User::firstOrCreate(
            ['email' => 'kasir@armalcellular.test'],
            [
                'username' => 'Kasir Armal',
                'password' => Hash::make('password'),
                'role_id'  => $kasirRole->id,
                'store_id' => $store->id,
                'contact'  => '081234567891',
                'status'   => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'teknisi@armalcellular.test'],
            [
                'username' => 'Teknisi Armal',
                'password' => Hash::make('password'),
                'role_id'  => $teknisiRole->id,
                'store_id' => $store->id,
                'contact'  => '081234567892',
                'status'   => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'gudang@armalcellular.test'],
            [
                'username' => 'Gudang Armal',
                'password' => Hash::make('password'),
                'role_id'  => $gudangRole->id,
                'store_id' => $store->id,
                'contact'  => '081234567893',
                'status'   => 'active',
            ]
        );

        // Bind store in app container for multi-tenancy traits
        app()->instance('current_store', $store);
    }
}
