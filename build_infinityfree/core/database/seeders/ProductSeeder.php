<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $products = [
            // ─── Kategori CAT0001: Voucher Internet & Kuota (Digital) ─────────────────
            ['code' => 'PRD00001', 'name' => 'Voucher Telkomsel 3GB / 5 Hari', 'category' => 'CAT0001', 'supplier' => 'SUP0002', 'buy' => 12000, 'sell' => 15000, 'stock' => 45, 'min' => 10, 'unit' => 'voucher', 'type' => 'digital', 'desc' => 'Voucher kuota internet Telkomsel 3GB berlaku 5 hari 24 jam all jaringan.'],
            ['code' => 'PRD00002', 'name' => 'Voucher Indosat Freedom 5GB / 30 Hari', 'category' => 'CAT0001', 'supplier' => 'SUP0002', 'buy' => 22000, 'sell' => 26000, 'stock' => 35, 'min' => 10, 'unit' => 'voucher', 'type' => 'digital', 'desc' => 'Voucher gosok Indosat kuota utama 5GB full 30 hari tanpa pembagian waktu.'],
            ['code' => 'PRD00003', 'name' => 'Voucher XL Data 8GB / 30 Hari', 'category' => 'CAT0001', 'supplier' => 'SUP0002', 'buy' => 32000, 'sell' => 37000, 'stock' => 25, 'min' => 8, 'unit' => 'voucher', 'type' => 'digital', 'desc' => 'Voucher internet XL kuota nasional 8GB berlaku selama 30 hari.'],
            ['code' => 'PRD00004', 'name' => 'Voucher Tri AON 6GB / 30 Hari', 'category' => 'CAT0001', 'supplier' => 'SUP0002', 'buy' => 18000, 'sell' => 22000, 'stock' => 30, 'min' => 10, 'unit' => 'voucher', 'type' => 'digital', 'desc' => 'Voucher internet Tri AlwaysOn 6GB dengan masa aktif panjang.'],
            ['code' => 'PRD00005', 'name' => 'Voucher Smartfren Unlimited 7 Hari', 'category' => 'CAT0001', 'supplier' => 'SUP0002', 'buy' => 15000, 'sell' => 18000, 'stock' => 20, 'min' => 5, 'unit' => 'voucher', 'type' => 'digital', 'desc' => 'Voucher Smartfren unlimited fup harian 7 hari.'],
            ['code' => 'PRD00006', 'name' => 'Voucher Telkomsel 10GB / 7 Hari', 'category' => 'CAT0001', 'supplier' => 'SUP0002', 'buy' => 25000, 'sell' => 30000, 'stock' => 15, 'min' => 5, 'unit' => 'voucher', 'type' => 'digital', 'desc' => 'Voucher kuota 10GB berlaku 7 hari.'],

            // ─── Kategori CAT0002: Pulsa Elektrik & Token (Digital) ───────────────────
            ['code' => 'PRD00007', 'name' => 'Pulsa Reguler 20.000 All Operator', 'category' => 'CAT0002', 'supplier' => 'SUP0002', 'buy' => 20200, 'sell' => 22000, 'stock' => 100, 'min' => 20, 'unit' => 'trx', 'type' => 'digital', 'desc' => 'Isi ulang pulsa reguler nominal 20.000.'],
            ['code' => 'PRD00008', 'name' => 'Pulsa Reguler 50.000 All Operator', 'category' => 'CAT0002', 'supplier' => 'SUP0002', 'buy' => 49800, 'sell' => 52000, 'stock' => 100, 'min' => 20, 'unit' => 'trx', 'type' => 'digital', 'desc' => 'Isi ulang pulsa reguler nominal 50.000.'],
            ['code' => 'PRD00009', 'name' => 'Token Listrik PLN 20.000', 'category' => 'CAT0002', 'supplier' => 'SUP0002', 'buy' => 20100, 'sell' => 22000, 'stock' => 50, 'min' => 10, 'unit' => 'trx', 'type' => 'digital', 'desc' => 'Token PLN prabayar 20.000.'],
            ['code' => 'PRD00010', 'name' => 'Token Listrik PLN 50.000', 'category' => 'CAT0002', 'supplier' => 'SUP0002', 'buy' => 50100, 'sell' => 52000, 'stock' => 50, 'min' => 10, 'unit' => 'trx', 'type' => 'digital', 'desc' => 'Token PLN prabayar 50.000.'],
            ['code' => 'PRD00011', 'name' => 'Pulsa Reguler 10.000 Telkomsel', 'category' => 'CAT0002', 'supplier' => 'SUP0002', 'buy' => 10200, 'sell' => 12000, 'stock' => 80, 'min' => 10, 'unit' => 'trx', 'type' => 'digital', 'desc' => 'Pulsa 10k Telkomsel.'],
            ['code' => 'PRD00012', 'name' => 'Pulsa Reguler 10.000 Indosat', 'category' => 'CAT0002', 'supplier' => 'SUP0002', 'buy' => 10100, 'sell' => 12000, 'stock' => 80, 'min' => 10, 'unit' => 'trx', 'type' => 'digital', 'desc' => 'Pulsa 10k Indosat.'],

            // ─── Kategori CAT0003: Kartu Perdana & Nomor Cantik (Physical) ────────────
            ['code' => 'PRD00013', 'name' => 'Kartu Perdana Telkomsel 10GB', 'category' => 'CAT0003', 'supplier' => 'SUP0004', 'buy' => 28000, 'sell' => 35000, 'stock' => 15, 'min' => 5, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kartu perdana Telkomsel 10GB.'],
            ['code' => 'PRD00014', 'name' => 'Kartu Perdana Indosat 15GB', 'category' => 'CAT0003', 'supplier' => 'SUP0004', 'buy' => 35000, 'sell' => 42000, 'stock' => 15, 'min' => 5, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kartu perdana Indosat 15GB.'],
            ['code' => 'PRD00015', 'name' => 'Kartu Perdana XL 10GB', 'category' => 'CAT0003', 'supplier' => 'SUP0004', 'buy' => 30000, 'sell' => 38000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kartu perdana XL 10GB.'],
            ['code' => 'PRD00016', 'name' => 'Kartu Perdana Tri 12GB', 'category' => 'CAT0003', 'supplier' => 'SUP0004', 'buy' => 25000, 'sell' => 32000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kartu perdana Tri 12GB.'],
            ['code' => 'PRD00017', 'name' => 'Kartu Perdana Smartfren 5GB', 'category' => 'CAT0003', 'supplier' => 'SUP0004', 'buy' => 20000, 'sell' => 27000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kartu perdana Smartfren 5GB.'],
            ['code' => 'PRD00018', 'name' => 'Kartu Perdana Kosong Telkomsel', 'category' => 'CAT0003', 'supplier' => 'SUP0004', 'buy' => 5000, 'sell' => 10000, 'stock' => 50, 'min' => 10, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kartu perdana kosong.'],

            // ─── Kategori CAT0004: Charger & Kabel Data (Physical) ───────────────────
            ['code' => 'PRD00019', 'name' => 'Kabel Data Type-C 3A', 'category' => 'CAT0004', 'supplier' => 'SUP0005', 'buy' => 12000, 'sell' => 25000, 'stock' => 40, 'min' => 10, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kabel data Type-C.'],
            ['code' => 'PRD00020', 'name' => 'Kabel Data Micro USB 2A', 'category' => 'CAT0004', 'supplier' => 'SUP0005', 'buy' => 10000, 'sell' => 20000, 'stock' => 30, 'min' => 8, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kabel data Micro USB.'],
            ['code' => 'PRD00021', 'name' => 'Kabel Lightning iPhone 20W', 'category' => 'CAT0004', 'supplier' => 'SUP0005', 'buy' => 25000, 'sell' => 45000, 'stock' => 20, 'min' => 5, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kabel Lightning.'],
            ['code' => 'PRD00022', 'name' => 'Charger 20W PD', 'category' => 'CAT0004', 'supplier' => 'SUP0005', 'buy' => 35000, 'sell' => 60000, 'stock' => 25, 'min' => 6, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Charger PD 20W.'],
            ['code' => 'PRD00023', 'name' => 'Adaptor Charger 2.4A', 'category' => 'CAT0004', 'supplier' => 'SUP0005', 'buy' => 20000, 'sell' => 35000, 'stock' => 15, 'min' => 5, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Adaptor 2.4A.'],
            ['code' => 'PRD00024', 'name' => 'Kabel Data Type-C to Type-C', 'category' => 'CAT0004', 'supplier' => 'SUP0005', 'buy' => 15000, 'sell' => 30000, 'stock' => 20, 'min' => 5, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kabel C to C.'],

            // ─── Kategori CAT0005: Aksesoris (Physical) ─────────────────────────────
            ['code' => 'PRD00025', 'name' => 'Tempered Glass Bening', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 5000, 'sell' => 15000, 'stock' => 65, 'min' => 15, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'TG Bening.'],
            ['code' => 'PRD00026', 'name' => 'Tempered Glass Privacy', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 12000, 'sell' => 30000, 'stock' => 35, 'min' => 10, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'TG Privacy.'],
            ['code' => 'PRD00027', 'name' => 'Softcase Anti-Crack', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 6000, 'sell' => 15000, 'stock' => 40, 'min' => 10, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Softcase.'],
            ['code' => 'PRD00028', 'name' => 'Holder Motor', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 28000, 'sell' => 50000, 'stock' => 12, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Holder Motor.'],
            ['code' => 'PRD00029', 'name' => 'Holder Mobil', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 25000, 'sell' => 45000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Holder Mobil.'],
            ['code' => 'PRD00030', 'name' => 'Ring Holder HP', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 3000, 'sell' => 8000, 'stock' => 100, 'min' => 20, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Ring Holder.'],
            ['code' => 'PRD00031', 'name' => 'Iring Magnet', 'category' => 'CAT0005', 'supplier' => 'SUP0001', 'buy' => 4000, 'sell' => 10000, 'stock' => 50, 'min' => 10, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Iring Magnet.'],

            // ─── Kategori CAT0006: Audio (Physical) ───────────────────────────────
            ['code' => 'PRD00032', 'name' => 'Earphone Stereo 3.5mm', 'category' => 'CAT0006', 'supplier' => 'SUP0005', 'buy' => 15000, 'sell' => 25000, 'stock' => 25, 'min' => 6, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Earphone 3.5mm.'],
            ['code' => 'PRD00033', 'name' => 'TWS Bluetooth Mini', 'category' => 'CAT0006', 'supplier' => 'SUP0005', 'buy' => 55000, 'sell' => 95000, 'stock' => 12, 'min' => 4, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'TWS Bluetooth.'],
            ['code' => 'PRD00034', 'name' => 'Headset Bluetooth Neckband', 'category' => 'CAT0006', 'supplier' => 'SUP0005', 'buy' => 45000, 'sell' => 80000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Headset Neckband.'],
            ['code' => 'PRD00035', 'name' => 'Kabel Aux 1.5m', 'category' => 'CAT0006', 'supplier' => 'SUP0005', 'buy' => 7000, 'sell' => 15000, 'stock' => 30, 'min' => 5, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Kabel Aux.'],
            ['code' => 'PRD00036', 'name' => 'Adapter Jack 3.5 to Lightning', 'category' => 'CAT0006', 'supplier' => 'SUP0005', 'buy' => 10000, 'sell' => 25000, 'stock' => 15, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Adapter Lightning.'],
            ['code' => 'PRD00037', 'name' => 'Speaker Bluetooth Portable', 'category' => 'CAT0006', 'supplier' => 'SUP0005', 'buy' => 60000, 'sell' => 110000, 'stock' => 8, 'min' => 2, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Speaker BT.'],

            // ─── Kategori CAT0007: Powerbank & Baterai (Physical) ───────────────────
            ['code' => 'PRD00038', 'name' => 'Powerbank 10.000mAh', 'category' => 'CAT0007', 'supplier' => 'SUP0005', 'buy' => 75000, 'sell' => 125000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'PB 10k.'],
            ['code' => 'PRD00039', 'name' => 'Baterai Double Power', 'category' => 'CAT0007', 'supplier' => 'SUP0003', 'buy' => 40000, 'sell' => 75000, 'stock' => 12, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Baterai.'],
            ['code' => 'PRD00040', 'name' => 'Powerbank 20.000mAh', 'category' => 'CAT0007', 'supplier' => 'SUP0005', 'buy' => 140000, 'sell' => 220000, 'stock' => 5, 'min' => 2, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'PB 20k.'],
            ['code' => 'PRD00041', 'name' => 'Baterai iPhone Original', 'category' => 'CAT0007', 'supplier' => 'SUP0003', 'buy' => 90000, 'sell' => 150000, 'stock' => 6, 'min' => 2, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'Baterai iP.'],
            ['code' => 'PRD00042', 'name' => 'Powerbank Mini 5000mAh', 'category' => 'CAT0007', 'supplier' => 'SUP0005', 'buy' => 50000, 'sell' => 90000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'physical', 'desc' => 'PB Mini.'],

            // ─── Kategori CAT0008: Sparepart (Sparepart) ───────────────
            ['code' => 'PRD00043', 'name' => 'LCD Redmi Note 10', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 165000, 'sell' => 250000, 'stock' => 8, 'min' => 2, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'LCD Redmi.'],
            ['code' => 'PRD00044', 'name' => 'LCD Samsung A03s', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 140000, 'sell' => 220000, 'stock' => 6, 'min' => 2, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'LCD Samsung.'],
            ['code' => 'PRD00045', 'name' => 'Konektor Cas Type-C', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 8000, 'sell' => 25000, 'stock' => 20, 'min' => 5, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'Konektor.'],
            ['code' => 'PRD00046', 'name' => 'Flexible Tombol On/Off', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 5000, 'sell' => 15000, 'stock' => 15, 'min' => 5, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'Flexible.'],
            ['code' => 'PRD00047', 'name' => 'Kamera Depan Universal', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 25000, 'sell' => 50000, 'stock' => 5, 'min' => 2, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'Kamera.'],
            ['code' => 'PRD00048', 'name' => 'Sim Tray HP Android', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 3000, 'sell' => 10000, 'stock' => 30, 'min' => 10, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'Sim Tray.'],
            ['code' => 'PRD00049', 'name' => 'Ic Cas Universal', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 15000, 'sell' => 35000, 'stock' => 10, 'min' => 3, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'Ic Cas.'],
            ['code' => 'PRD00050', 'name' => 'Speaker Buzzer', 'category' => 'CAT0008', 'supplier' => 'SUP0003', 'buy' => 10000, 'sell' => 25000, 'stock' => 15, 'min' => 5, 'unit' => 'pcs', 'type' => 'sparepart', 'desc' => 'Speaker.'],
        ];

        foreach ($products as $p) {
            Product::firstOrCreate(
                ['product_code' => $p['code']],
                [
                    'store_id'       => $store?->id,
                    'name'           => $p['name'],
                    'category_code'  => $p['category'],
                    'supplier_code'  => $p['supplier'],
                    'purchase_price' => $p['buy'],
                    'selling_price'  => $p['sell'],
                    'stock'          => $p['stock'],
                    'minimum_stock'  => $p['min'],
                    'unit'           => $p['unit'],
                    'description'    => $p['desc'],
                    'type'           => $p['type'],
                    'status'         => 'active',
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}