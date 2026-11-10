<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\PaymentSetting;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder khusus untuk Laravel Dusk Blackbox Testing.
 * Membuat data fixture yang diperlukan oleh semua test case EP dan BVA.
 *
 * Akun yang dibuat:
 *   owner@test.dusk    / password
 *   kasir@test.dusk    / password
 *   teknisi@test.dusk  / password
 *   gudang@test.dusk   / password
 *   inactive@test.dusk / password  (status: inactive)
 */
class DuskTestSeeder extends Seeder
{
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────────────────────
        $ownerRole   = Role::firstOrCreate(['name' => 'owner']);
        $kasirRole   = Role::firstOrCreate(['name' => 'kasir']);
        $teknisiRole = Role::firstOrCreate(['name' => 'teknisi']);
        $gudangRole  = Role::firstOrCreate(['name' => 'gudang']);

        // ── Store ──────────────────────────────────────────────────────────
        $store = Store::firstOrCreate(
            ['slug' => 'dusk-test-store'],
            [
                'name'            => 'Dusk Test Store',
                'is_active'       => true,
                'catalog_enabled' => true,
            ]
        );

        // ── Users ──────────────────────────────────────────────────────────
        $owner = User::firstOrCreate(
            ['email' => 'owner@test.dusk'],
            [
                'username' => 'Owner Dusk',
                'password' => Hash::make('password'),
                'role_id'  => $ownerRole->id,
                'store_id' => $store->id,
                'contact'  => '081111111111',
                'status'   => 'active',
            ]
        );

        // Link owner ke store
        if (!$store->owner_id) {
            $store->update(['owner_id' => $owner->id]);
        }

        User::firstOrCreate(
            ['email' => 'kasir@test.dusk'],
            [
                'username' => 'Kasir Dusk',
                'password' => Hash::make('password'),
                'role_id'  => $kasirRole->id,
                'store_id' => $store->id,
                'contact'  => '082222222222',
                'status'   => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'teknisi@test.dusk'],
            [
                'username' => 'Teknisi Dusk',
                'password' => Hash::make('password'),
                'role_id'  => $teknisiRole->id,
                'store_id' => $store->id,
                'contact'  => '083333333333',
                'status'   => 'active',
            ]
        );

        User::firstOrCreate(
            ['email' => 'gudang@test.dusk'],
            [
                'username' => 'Gudang Dusk',
                'password' => Hash::make('password'),
                'role_id'  => $gudangRole->id,
                'store_id' => $store->id,
                'contact'  => '084444444444',
                'status'   => 'active',
            ]
        );

        // Akun tidak aktif — untuk test EP-LOGIN-004
        User::firstOrCreate(
            ['email' => 'inactive@test.dusk'],
            [
                'username' => 'Inactive Dusk',
                'password' => Hash::make('password'),
                'role_id'  => $kasirRole->id,
                'store_id' => $store->id,
                'contact'  => '085555555555',
                'status'   => 'inactive',
            ]
        );

        // ── Settings ───────────────────────────────────────────────────────
        app()->instance('current_store', $store);
        Setting::set('store_name', 'Dusk Test Store');
        Setting::set('receipt_footer', 'Terima kasih telah berbelanja.');
        Setting::set('tax_percentage', '0');
        Setting::set('default_currency', 'IDR');

        PaymentSetting::firstOrCreate(
            ['store_id' => $store->id],
            ['qris_mode' => 'manual', 'is_qris_active' => true]
        );

        // ── Kategori ───────────────────────────────────────────────────────
        $catProduk = Category::firstOrCreate(
            ['slug' => 'elektronik-dusk'],
            [
                'category_code' => 'CAT-DUSK-001',
                'name'          => 'Elektronik Dusk',
                'type'          => 'product',
                'is_active'     => true,
                'store_id'      => $store->id,
            ]
        );

        $catSparepart = Category::firstOrCreate(
            ['slug' => 'sparepart-dusk'],
            [
                'category_code' => 'CAT-DUSK-002',
                'name'          => 'Sparepart Dusk',
                'type'          => 'product',
                'is_active'     => true,
                'store_id'      => $store->id,
            ]
        );

        $catService = Category::firstOrCreate(
            ['slug' => 'servis-dusk'],
            [
                'category_code' => 'CAT-DUSK-003',
                'name'          => 'Servis Dusk',
                'type'          => 'service',
                'is_active'     => true,
                'store_id'      => $store->id,
            ]
        );

        // ── Supplier ───────────────────────────────────────────────────────
        Supplier::firstOrCreate(
            ['supplier_code' => 'SUP-DUSK-001'],
            [
                'name'      => 'Supplier Dusk Test',
                'is_active' => true,
                'store_id'  => $store->id,
            ]
        );

        // ── Produk — untuk testing BVA dan EP kasir/transaksi ─────────────
        // Produk dengan stok cukup (100 unit) — untuk kasir checkout
        Product::firstOrCreate(
            ['product_code' => 'PRD-DUSK-001'],
            [
                'name'           => 'Produk Dusk Aktif',
                'category_code'  => $catProduk->category_code,
                'purchase_price' => 50000,
                'selling_price'  => 75000,
                'stock'          => 100,
                'minimum_stock'  => 5,
                'unit'           => 'pcs',
                'type'           => 'physical',
                'status'         => 'active',
                'store_id'       => $store->id,
            ]
        );

        // Produk dengan stok 1 — untuk BVA stok batas minimum
        Product::firstOrCreate(
            ['product_code' => 'PRD-DUSK-002'],
            [
                'name'           => 'Produk Dusk Stok Satu',
                'category_code'  => $catProduk->category_code,
                'purchase_price' => 10000,
                'selling_price'  => 15000,
                'stock'          => 1,
                'minimum_stock'  => 1,
                'unit'           => 'pcs',
                'type'           => 'physical',
                'status'         => 'active',
                'store_id'       => $store->id,
            ]
        );

        // Sparepart — untuk service repair
        Product::firstOrCreate(
            ['product_code' => 'SPR-DUSK-001'],
            [
                'name'           => 'Sparepart Dusk Test',
                'category_code'  => $catSparepart->category_code,
                'purchase_price' => 25000,
                'selling_price'  => 35000,
                'stock'          => 50,
                'minimum_stock'  => 2,
                'unit'           => 'pcs',
                'type'           => 'sparepart',
                'status'         => 'active',
                'store_id'       => $store->id,
            ]
        );
    }
}
