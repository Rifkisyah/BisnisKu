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

        // ─── Toko 1: Armal Cellular ─────────────────────────────────────────
        $store1 = Store::firstOrCreate(
            ['slug' => 'armal-cellular'],
            [
                'name'            => 'Armal Cellular',
                'address'         => 'Jl. Pahlawan No. 123, Jakarta Selatan',
                'phone'           => '081234567890',
                'email'           => 'info@armalcellular.com',
                'description'     => 'Toko handphone & aksesoris terpercaya sejak 2010.',
                'is_active'       => true,
                'catalog_enabled' => true,
            ]
        );

        $owner1 = User::firstOrCreate(
            ['email' => 'owner@armalcellular.test'],
            [
                'username' => 'Owner Armal',
                'password' => Hash::make('password'),
                'role_id'  => $ownerRole->id,
                'store_id' => $store1->id,
                'contact'  => '081234567890',
                'status'   => 'active',
            ]
        );

        // Link owner to store
        $store1->update(['owner_id' => $owner1->id]);

        // Staf Toko 1
        User::firstOrCreate(
            ['email' => 'kasir@armalcellular.test'],
            ['username' => 'Kasir Armal', 'password' => Hash::make('password'), 'role_id' => $kasirRole->id, 'store_id' => $store1->id, 'contact' => '081234567891', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'teknisi@armalcellular.test'],
            ['username' => 'Teknisi Armal', 'password' => Hash::make('password'), 'role_id' => $teknisiRole->id, 'store_id' => $store1->id, 'contact' => '081234567892', 'status' => 'active']
        );
        User::firstOrCreate(
            ['email' => 'gudang@armalcellular.test'],
            ['username' => 'Gudang Armal', 'password' => Hash::make('password'), 'role_id' => $gudangRole->id, 'store_id' => $store1->id, 'contact' => '081234567893', 'status' => 'active']
        );

        // Default settings for Toko 1
        app()->instance('current_store', $store1);
        \App\Models\Setting::set('store_name', 'Armal Cellular');
        \App\Models\Setting::set('store_address', 'Jl. Pahlawan No. 123, Jakarta Selatan');
        \App\Models\Setting::set('store_phone', '081234567890');
        \App\Models\Setting::set('store_email', 'info@armalcellular.com');
        \App\Models\Setting::set('receipt_footer', 'Terima kasih telah berbelanja di Armal Cellular. Barang yang sudah dibeli tidak dapat dikembalikan.');
        \App\Models\Setting::set('tax_percentage', '0');
        \App\Models\Setting::set('default_currency', 'IDR');

        \App\Models\PaymentSetting::firstOrCreate(
            ['store_id' => $store1->id],
            ['qris_mode' => 'manual', 'is_qris_active' => true]
        );

        // ─── Toko 2: Demo Elektronik ─────────────────────────────────────────
        $store2 = Store::firstOrCreate(
            ['slug' => 'demo-elektronik'],
            [
                'name'            => 'Demo Elektronik',
                'address'         => 'Jl. Merdeka No. 456, Bandung',
                'phone'           => '022-1234567',
                'email'           => 'demo@elektronik.test',
                'description'     => 'Toko elektronik demo untuk keperluan pengujian.',
                'is_active'       => true,
                'catalog_enabled' => true,
            ]
        );

        $owner2 = User::firstOrCreate(
            ['email' => 'owner@demo-elektronik.test'],
            [
                'username' => 'Owner Demo',
                'password' => Hash::make('password'),
                'role_id'  => $ownerRole->id,
                'store_id' => $store2->id,
                'contact'  => '022-1234567',
                'status'   => 'active',
            ]
        );

        $store2->update(['owner_id' => $owner2->id]);

        // Default settings for Toko 2
        app()->instance('current_store', $store2);
        \App\Models\Setting::set('store_name', 'Demo Elektronik');
        \App\Models\Setting::set('receipt_footer', 'Terima kasih telah berbelanja di Demo Elektronik.');
        \App\Models\Setting::set('tax_percentage', '0');
        \App\Models\Setting::set('default_currency', 'IDR');

        \App\Models\PaymentSetting::firstOrCreate(
            ['store_id' => $store2->id],
            ['qris_mode' => 'manual', 'is_qris_active' => true]
        );

        // Clear tenant context after seeding
        app()->forgetInstance('current_store');
    }
}
