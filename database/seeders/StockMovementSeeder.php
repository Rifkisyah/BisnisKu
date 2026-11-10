<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;

class StockMovementSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $products = Product::where('store_id', $store?->id)->get();
        $user = User::where('store_id', $store?->id)->first() ?? User::first();
        if ($products->isEmpty() || !$user) return;

        // 50 pergerakan stok eksplisit mencakup semua tipe: in (22), out (18), adjustment (10)
        $movements = [
            // ─── TYPE: IN (22 data) ──────────────────────────────────────────────────
            ['code'=>'MOV0001','prod'=>'PRD00001','type'=>'in', 'qty'=>20,'days_ago'=>78,'notes'=>'Stok masuk voucher Telkomsel 3GB dari kulakan Distributor Nusantara'],
            ['code'=>'MOV0002','prod'=>'PRD00004','type'=>'in', 'qty'=>15,'days_ago'=>75,'notes'=>'Restock voucher Tri AON dari agen resmi'],
            ['code'=>'MOV0003','prod'=>'PRD00025','type'=>'in', 'qty'=>30,'days_ago'=>73,'notes'=>'Tempered glass bening diterima dari Grosir Roxy Mas'],
            ['code'=>'MOV0004','prod'=>'PRD00027','type'=>'in', 'qty'=>25,'days_ago'=>70,'notes'=>'Softcase Anti-Crack masuk dari supplier Roxy'],
            ['code'=>'MOV0005','prod'=>'PRD00019','type'=>'in', 'qty'=>15,'days_ago'=>68,'notes'=>'Kabel Type-C 3A diterima dari Vivan Authorized Dealer'],
            ['code'=>'MOV0006','prod'=>'PRD00022','type'=>'in', 'qty'=>10,'days_ago'=>65,'notes'=>'Charger 20W PD masuk dari Vivan - packing aman'],
            ['code'=>'MOV0007','prod'=>'PRD00043','type'=>'in', 'qty'=>5, 'days_ago'=>62,'notes'=>'LCD Redmi Note 10 diterima dari Mitra Sparepart Tokopedia'],
            ['code'=>'MOV0008','prod'=>'PRD00044','type'=>'in', 'qty'=>4, 'days_ago'=>58,'notes'=>'LCD Samsung A03s diterima - original verified'],
            ['code'=>'MOV0009','prod'=>'PRD00013','type'=>'in', 'qty'=>20,'days_ago'=>55,'notes'=>'Kartu perdana Telkomsel 10GB kulakan dari agen'],
            ['code'=>'MOV0010','prod'=>'PRD00014','type'=>'in', 'qty'=>15,'days_ago'=>50,'notes'=>'Kartu perdana Indosat 15GB diterima dari agen Matraman'],
            ['code'=>'MOV0011','prod'=>'PRD00007','type'=>'in', 'qty'=>50,'days_ago'=>48,'notes'=>'Pulsa 20rb all operator restock dari Distributor Nusantara'],
            ['code'=>'MOV0012','prod'=>'PRD00009','type'=>'in', 'qty'=>30,'days_ago'=>45,'notes'=>'Token listrik PLN 20rb restock mingguan'],
            ['code'=>'MOV0013','prod'=>'PRD00026','type'=>'in', 'qty'=>20,'days_ago'=>40,'notes'=>'Tempered Glass Privacy masuk dari Roxy Mas - semua OK'],
            ['code'=>'MOV0014','prod'=>'PRD00024','type'=>'in', 'qty'=>25,'days_ago'=>38,'notes'=>'Kabel C to C restock dari Vivan Dealer'],
            ['code'=>'MOV0015','prod'=>'PRD00033','type'=>'in', 'qty'=>8, 'days_ago'=>30,'notes'=>'TWS Bluetooth mini diterima dari Vivan - unit tested normal'],
            ['code'=>'MOV0016','prod'=>'PRD00032','type'=>'in', 'qty'=>10,'days_ago'=>28,'notes'=>'Earphone kabel stereo masuk dari Vivan'],
            ['code'=>'MOV0017','prod'=>'PRD00039','type'=>'in', 'qty'=>8, 'days_ago'=>20,'notes'=>'Baterai double power diterima dari Mitra Sparepart Shopee'],
            ['code'=>'MOV0018','prod'=>'PRD00045','type'=>'in', 'qty'=>15,'days_ago'=>18,'notes'=>'Konektor charger Type-C masuk dari Mitra Sparepart'],
            ['code'=>'MOV0019','prod'=>'PRD00001','type'=>'in', 'qty'=>25,'days_ago'=>10,'notes'=>'Restock voucher Telkomsel 3GB mingguan dari Distributor'],
            ['code'=>'MOV0020','prod'=>'PRD00002','type'=>'in', 'qty'=>20,'days_ago'=>8, 'notes'=>'Voucher Indosat Freedom 5GB restock dari agen'],
            ['code'=>'MOV0021','prod'=>'PRD00038','type'=>'in', 'qty'=>5, 'days_ago'=>5, 'notes'=>'Powerbank 10k masuk dari Vivan Dealer - kondisi baru'],
            ['code'=>'MOV0022','prod'=>'PRD00041','type'=>'in', 'qty'=>3, 'days_ago'=>3, 'notes'=>'Baterai iPhone diterima dari Mitra Sparepart - garansi toko'],

            // ─── TYPE: OUT (18 data) ─────────────────────────────────────────────────
            ['code'=>'MOV0023','prod'=>'PRD00037','type'=>'out','qty'=>1, 'days_ago'=>76,'notes'=>'Speaker Bluetooth rusak saat pengetesan teknisi - dikeluarkan'],
            ['code'=>'MOV0024','prod'=>'PRD00024','type'=>'out','qty'=>2, 'days_ago'=>72,'notes'=>'Kabel C to C pemakaian internal konter - charger display'],
            ['code'=>'MOV0025','prod'=>'PRD00025','type'=>'out','qty'=>2, 'days_ago'=>68,'notes'=>'Tempered glass bening rusak kemasan saat display etalase'],
            ['code'=>'MOV0026','prod'=>'PRD00039','type'=>'out','qty'=>1, 'days_ago'=>64,'notes'=>'Baterai double power dipakai teknisi untuk servis garansi toko'],
            ['code'=>'MOV0027','prod'=>'PRD00045','type'=>'out','qty'=>1, 'days_ago'=>60,'notes'=>'Konektor charger digunakan untuk demo kepada pelanggan'],
            ['code'=>'MOV0028','prod'=>'PRD00019','type'=>'out','qty'=>1, 'days_ago'=>55,'notes'=>'Kabel Type-C untuk display konter meja kasir'],
            ['code'=>'MOV0029','prod'=>'PRD00026','type'=>'out','qty'=>2, 'days_ago'=>50,'notes'=>'Tempered Glass Privacy retur dari pelanggan garansi - kondisi cacat produksi'],
            ['code'=>'MOV0030','prod'=>'PRD00027','type'=>'out','qty'=>3, 'days_ago'=>45,'notes'=>'Softcase cacat saat pengiriman - diretur ke supplier'],
            ['code'=>'MOV0031','prod'=>'PRD00044','type'=>'out','qty'=>1, 'days_ago'=>40,'notes'=>'LCD Samsung retak saat instalasi di unit servis teknisi'],
            ['code'=>'MOV0032','prod'=>'PRD00022','type'=>'out','qty'=>1, 'days_ago'=>35,'notes'=>'Charger 20W PD dipakai tester produk kasir'],
            ['code'=>'MOV0033','prod'=>'PRD00033','type'=>'out','qty'=>1, 'days_ago'=>30,'notes'=>'TWS Bluetooth unit display etalase konter'],
            ['code'=>'MOV0034','prod'=>'PRD00013','type'=>'out','qty'=>2, 'days_ago'=>25,'notes'=>'Kartu perdana kadaluarsa masa berlaku - dikeluarkan dari stok'],
            ['code'=>'MOV0035','prod'=>'PRD00001','type'=>'out','qty'=>3, 'days_ago'=>20,'notes'=>'Voucher dipakai promo testing aktivasi internet oleh kasir'],
            ['code'=>'MOV0036','prod'=>'PRD00043','type'=>'out','qty'=>1, 'days_ago'=>15,'notes'=>'LCD Redmi rusak saat quality check - tidak bisa dijual'],
            ['code'=>'MOV0037','prod'=>'PRD00007','type'=>'out','qty'=>5, 'days_ago'=>10,'notes'=>'Pulsa 20rb untuk operasional WA bisnis konter bulanan'],
            ['code'=>'MOV0038','prod'=>'PRD00032','type'=>'out','qty'=>1, 'days_ago'=>7, 'notes'=>'Earphone display konter - kabel putus saat demo pelanggan'],
            ['code'=>'MOV0039','prod'=>'PRD00014','type'=>'out','qty'=>1, 'days_ago'=>4, 'notes'=>'Kartu perdana Indosat digunakan untuk test jaringan konter'],
            ['code'=>'MOV0040','prod'=>'PRD00041','type'=>'out','qty'=>1, 'days_ago'=>2, 'notes'=>'Baterai iPhone rusak saat inspeksi awal - dikeluarkan'],

            // ─── TYPE: ADJUSTMENT (10 data) ──────────────────────────────────────────
            ['code'=>'MOV0041','prod'=>'PRD00001','type'=>'adjustment','qty'=>5, 'days_ago'=>74,'notes'=>'Koreksi stok awal voucher Telkomsel - selisih hasil opname bulanan'],
            ['code'=>'MOV0042','prod'=>'PRD00009','type'=>'adjustment','qty'=>10,'days_ago'=>66,'notes'=>'Penyesuaian stok token listrik PLN setelah audit gudang'],
            ['code'=>'MOV0043','prod'=>'PRD00025','type'=>'adjustment','qty'=>-3,'days_ago'=>58,'notes'=>'Koreksi minus tempered glass - ditemukan hilang saat opname'],
            ['code'=>'MOV0044','prod'=>'PRD00007','type'=>'adjustment','qty'=>15,'days_ago'=>52,'notes'=>'Rekonsolidasi stok pulsa setelah sistem maintenance'],
            ['code'=>'MOV0045','prod'=>'PRD00026','type'=>'adjustment','qty'=>-2,'days_ago'=>44,'notes'=>'Koreksi stok tempered glass privacy minus - selisih data fisik vs sistem'],
            ['code'=>'MOV0046','prod'=>'PRD00039','type'=>'adjustment','qty'=>3, 'days_ago'=>36,'notes'=>'Penambahan stok baterai setelah audit sparepart bulanan'],
            ['code'=>'MOV0047','prod'=>'PRD00013','type'=>'adjustment','qty'=>5, 'days_ago'=>26,'notes'=>'Koreksi positif kartu perdana - temukan stok tersembunyi di laci'],
            ['code'=>'MOV0048','prod'=>'PRD00045','type'=>'adjustment','qty'=>-1,'days_ago'=>18,'notes'=>'Koreksi minus konektor charger - hilang saat servis teknisi'],
            ['code'=>'MOV0049','prod'=>'PRD00002','type'=>'adjustment','qty'=>8, 'days_ago'=>12,'notes'=>'Penyesuaian stok voucher Indosat setelah rekonsiliasi POS'],
            ['code'=>'MOV0050','prod'=>'PRD00022','type'=>'adjustment','qty'=>-1,'days_ago'=>6, 'notes'=>'Koreksi charger PD minus - hilang dicek sedang di servis display'],
        ];

        $productMap = $products->keyBy('product_code');

        foreach ($movements as $m) {
            if (!isset($productMap[$m['prod']])) continue;
            $product = $productMap[$m['prod']];
            $curr    = $product->stock;
            $qty     = abs($m['qty']);
            $prev    = $m['type'] === 'in' ? max(0, $curr - $qty) : $curr + $qty;

            StockMovement::firstOrCreate(
                ['reference_code' => $m['code']],
                [
                    'store_id'       => $store?->id,
                    'product_code'   => $product->product_code,
                    'created_by'     => $user->id,
                    'type'           => $m['type'],
                    'total_stock'    => $qty,
                    'previous_stock' => $prev,
                    'current_stock'  => $curr,
                    'movement_date'  => now()->subDays($m['days_ago'])->setTime(rand(9, 18), rand(0, 59), 0),
                    'reference_type' => 'manual',
                    'notes'          => $m['notes'],
                ]
            );
        }

        app()->forgetInstance('current_store');
    }
}
