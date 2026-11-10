<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProductPurchaseSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $suppliers = Supplier::where('store_id', $store?->id)->get();
        $products  = Product::where('store_id', $store?->id)->get();
        $user = User::whereHas('role', function ($q) { $q->whereIn('name', ['owner', 'gudang']); })
            ->where('store_id', $store?->id)->first() ?? User::first();

        if ($suppliers->isEmpty() || $products->isEmpty() || !$user) return;

        // 50 pengadaan eksplisit mencakup semua status
        // draft (10), ordered (10), partial_received (10), received (10), cancelled (10)
        $purchases = [
            // ─── RECEIVED (10 data) ──────────────────────────────────────────────────
            ['code'=>'PPR20260601001','days_ago'=>80,'sup'=>'SUP0002','status'=>'received',        'source'=>'offline',    'notes'=>'Kulakan rutin voucher internet & pulsa dari Distributor Nusantara - diterima lengkap',          'items'=>[['PRD00001',20,'voucher'],['PRD00004',15,'voucher']]],
            ['code'=>'PPR20260605002','days_ago'=>76,'sup'=>'SUP0001','status'=>'received',        'source'=>'whatsapp',   'notes'=>'Restock aksesoris HP dari Grosir Roxy Mas - semua barang normal diterima',                   'items'=>[['PRD00025',30,'pcs'],['PRD00027',25,'pcs']]],
            ['code'=>'PPR20260610003','days_ago'=>71,'sup'=>'SUP0005','status'=>'received',        'source'=>'offline',    'notes'=>'Kulakan charger & kabel dari Vivan Authorized Dealer - packing aman',                         'items'=>[['PRD00019',15,'pcs'],['PRD00022',10,'pcs']]],
            ['code'=>'PPR20260615004','days_ago'=>66,'sup'=>'SUP0003','status'=>'received',        'source'=>'marketplace','notes'=>'Order sparepart LCD dari Mitra Sparepart via Tokopedia - barang original diterima',            'items'=>[['PRD00043',5,'pcs'],['PRD00044',4,'pcs']]],
            ['code'=>'PPR20260622005','days_ago'=>59,'sup'=>'SUP0004','status'=>'received',        'source'=>'offline',    'notes'=>'Kulakan kartu perdana dari Agen Telkomsel & Indosat - stok lengkap siap jual',                  'items'=>[['PRD00013',20,'pcs'],['PRD00014',15,'pcs']]],
            ['code'=>'PPR20260701006','days_ago'=>52,'sup'=>'SUP0002','status'=>'received',        'source'=>'whatsapp',   'notes'=>'Restock pulsa & token listrik dari Distributor Nusantara via WA - sudah diterima',             'items'=>[['PRD00007',50,'trx'],['PRD00009',30,'trx']]],
            ['code'=>'PPR20260710007','days_ago'=>43,'sup'=>'SUP0001','status'=>'received',        'source'=>'offline',    'notes'=>'Kulakan tempered glass & softcase dari Roxy Mas - diterima 100%',                             'items'=>[['PRD00026',20,'pcs'],['PRD00024',25,'pcs']]],
            ['code'=>'PPR20260720008','days_ago'=>33,'sup'=>'SUP0005','status'=>'received',        'source'=>'offline',    'notes'=>'Restock TWS & earphone dari Vivan Dealer - semua unit tested berfungsi normal',                'items'=>[['PRD00033',8,'pcs'],['PRD00032',10,'pcs']]],
            ['code'=>'PPR20260801009','days_ago'=>21,'sup'=>'SUP0003','status'=>'received',        'source'=>'marketplace','notes'=>'Order baterai & konektor dari Mitra Sparepart via Shopee - barang asli garansi toko',          'items'=>[['PRD00039',8,'pcs'],['PRD00045',15,'pcs']]],
            ['code'=>'PPR20260810010','days_ago'=>12,'sup'=>'SUP0002','status'=>'received',        'source'=>'offline',    'notes'=>'Kulakan mingguan voucher internet dari Distributor Nusantara - selesai dan diterima',           'items'=>[['PRD00001',25,'voucher'],['PRD00002',20,'voucher']]],

            // ─── PARTIAL_RECEIVED (10 data) ──────────────────────────────────────────
            ['code'=>'PPR20260603011','days_ago'=>78,'sup'=>'SUP0001','status'=>'partial_received','source'=>'offline',    'notes'=>'Kulakan aksesoris dari Roxy - 1 unit holder motor penyok kemasan, diretur',                    'items'=>[['PRD00028',10,'pcs'],['PRD00025',20,'pcs']]],
            ['code'=>'PPR20260612012','days_ago'=>69,'sup'=>'SUP0005','status'=>'partial_received','source'=>'whatsapp',   'notes'=>'Order charger via WA - sebagian belum terkirim menunggu stok supplier',                       'items'=>[['PRD00022',15,'pcs'],['PRD00020',10,'pcs']]],
            ['code'=>'PPR20260618013','days_ago'=>63,'sup'=>'SUP0003','status'=>'partial_received','source'=>'marketplace','notes'=>'Beli LCD dari marketplace - 1 unit LCD Samsung received sesuai, 1 lagi pending pengiriman',   'items'=>[['PRD00044',3,'pcs'],['PRD00043',3,'pcs']]],
            ['code'=>'PPR20260625014','days_ago'=>58,'sup'=>'SUP0004','status'=>'partial_received','source'=>'offline',    'notes'=>'Ambil kartu perdana dari agen - stok XL hanya 8 dari 15 yang dipesan, sisanya inden',          'items'=>[['PRD00015',15,'pcs'],['PRD00016',10,'pcs']]],
            ['code'=>'PPR20260705015','days_ago'=>48,'sup'=>'SUP0002','status'=>'partial_received','source'=>'offline',    'notes'=>'Restock voucher Smartfren - hanya 12 dari 20 tersedia hari ini',                               'items'=>[['PRD00005',20,'voucher'],['PRD00006',15,'voucher']]],
            ['code'=>'PPR20260715016','days_ago'=>38,'sup'=>'SUP0001','status'=>'partial_received','source'=>'whatsapp',   'notes'=>'Kulakan antigores hydrogel - 5 dari 15 unit cacat, dikirim retur ke supplier',                  'items'=>[['PRD00027',15,'pcs'],['PRD00026',20,'pcs']]],
            ['code'=>'PPR20260722017','days_ago'=>31,'sup'=>'SUP0005','status'=>'partial_received','source'=>'offline',    'notes'=>'Order powerbank - 3 dari 8 unit diterima duluan, 5 lagi dalam pengiriman',                      'items'=>[['PRD00038',8,'pcs'],['PRD00040',5,'pcs']]],
            ['code'=>'PPR20260802018','days_ago'=>20,'sup'=>'SUP0003','status'=>'partial_received','source'=>'marketplace','notes'=>'Beli sparepart dari Shopee - konektor charger received, IC cas masih diproses',                'items'=>[['PRD00045',10,'pcs'],['PRD00049',8,'pcs']]],
            ['code'=>'PPR20260808019','days_ago'=>14,'sup'=>'SUP0002','status'=>'partial_received','source'=>'offline',    'notes'=>'Restock pulsa - token listrik PLN semua received, pulsa XL masih sebagian',                    'items'=>[['PRD00010',30,'trx'],['PRD00011',20,'trx']]],
            ['code'=>'PPR20260812020','days_ago'=>10,'sup'=>'SUP0004','status'=>'partial_received','source'=>'whatsapp',   'notes'=>'Order kartu perdana via WA - Telkomsel received lengkap, Tri masih menunggu stok',              'items'=>[['PRD00013',10,'pcs'],['PRD00017',10,'pcs']]],

            // ─── ORDERED (10 data) ───────────────────────────────────────────────────
            ['code'=>'PPR20260609021','days_ago'=>72,'sup'=>'SUP0001','status'=>'ordered',         'source'=>'whatsapp',   'notes'=>'Pesanan aksesoris ke Roxy Mas via WA - menunggu konfirmasi pengiriman',                        'items'=>[['PRD00025',30,'pcs'],['PRD00022',20,'pcs']]],
            ['code'=>'PPR20260617022','days_ago'=>64,'sup'=>'SUP0005','status'=>'ordered',         'source'=>'offline',    'notes'=>'Order charger & kabel ke Vivan Dealer - sudah transfer DP, resi belum ada',                   'items'=>[['PRD00019',20,'pcs'],['PRD00021',15,'pcs']]],
            ['code'=>'PPR20260624023','days_ago'=>59,'sup'=>'SUP0003','status'=>'ordered',         'source'=>'marketplace','notes'=>'Pemesanan LCD ke Mitra Sparepart via Tokopedia - menunggu konfirmasi penjual',                 'items'=>[['PRD00043',4,'pcs'],['PRD00050',2,'pcs']]],
            ['code'=>'PPR20260702024','days_ago'=>51,'sup'=>'SUP0004','status'=>'ordered',         'source'=>'offline',    'notes'=>'Pesanan kartu perdana ke agen - tanda terima sudah dikirim, estimasi 3 hari',                   'items'=>[['PRD00013',20,'pcs'],['PRD00014',15,'pcs']]],
            ['code'=>'PPR20260712025','days_ago'=>41,'sup'=>'SUP0002','status'=>'ordered',         'source'=>'whatsapp',   'notes'=>'Order voucher internet & pulsa via WA Distributor - menunggu pengiriman',                      'items'=>[['PRD00003',20,'voucher'],['PRD00008',30,'trx']]],
            ['code'=>'PPR20260718026','days_ago'=>35,'sup'=>'SUP0001','status'=>'ordered',         'source'=>'offline',    'notes'=>'Kulakan ring holder & softcase dari Roxy - dalam proses packing supplier',                     'items'=>[['PRD00031',20,'pcs'],['PRD00024',25,'pcs']]],
            ['code'=>'PPR20260726027','days_ago'=>27,'sup'=>'SUP0005','status'=>'ordered',         'source'=>'whatsapp',   'notes'=>'Pesan TWS & earphone kabel ke Vivan via WA - menunggu stok dipacking',                        'items'=>[['PRD00033',6,'pcs'],['PRD00029',12,'pcs']]],
            ['code'=>'PPR20260804028','days_ago'=>18,'sup'=>'SUP0003','status'=>'ordered',         'source'=>'marketplace','notes'=>'Order baterai iPhone dari Mitra Sparepart via Shopee - menunggu proses seller',               'items'=>[['PRD00041',4,'pcs'],['PRD00048',3,'pcs']]],
            ['code'=>'PPR20260811029','days_ago'=>11,'sup'=>'SUP0002','status'=>'ordered',         'source'=>'offline',    'notes'=>'Pesan voucher Telkomsel 10GB & Indosat Freedom ke agen - estimasi besok datang',               'items'=>[['PRD00006',15,'voucher'],['PRD00002',20,'voucher']]],
            ['code'=>'PPR20260815030','days_ago'=>7, 'sup'=>'SUP0001','status'=>'ordered',         'source'=>'whatsapp',   'notes'=>'Order tempered glass privacy & antigores hydrogel via WA Roxy - sedang diproses',              'items'=>[['PRD00026',25,'pcs'],['PRD00027',20,'pcs']]],

            // ─── DRAFT (10 data) ─────────────────────────────────────────────────────
            ['code'=>'PPR20260606031','days_ago'=>75,'sup'=>'SUP0002','status'=>'draft',           'source'=>'offline',    'notes'=>'Draf rencana restock voucher internet mingguan - belum dikonfirmasi',                         'items'=>[['PRD00001',30,'voucher'],['PRD00004',20,'voucher']]],
            ['code'=>'PPR20260614032','days_ago'=>67,'sup'=>'SUP0001','status'=>'draft',           'source'=>'offline',    'notes'=>'Draf kulakan aksesoris akhir bulan - menunggu persetujuan owner',                             'items'=>[['PRD00025',25,'pcs'],['PRD00028',15,'pcs']]],
            ['code'=>'PPR20260620033','days_ago'=>63,'sup'=>'SUP0005','status'=>'draft',           'source'=>'whatsapp',   'notes'=>'Draf order charger kepala PD & kabel Type-C dari Vivan - menunggu final cek stok',            'items'=>[['PRD00022',20,'pcs'],['PRD00016',15,'pcs']]],
            ['code'=>'PPR20260630034','days_ago'=>53,'sup'=>'SUP0003','status'=>'draft',           'source'=>'marketplace','notes'=>'Draf pembelian sparepart LCD dari marketplace - menunggu perbandingan harga',                  'items'=>[['PRD00044',3,'pcs'],['PRD00046',5,'pcs']]],
            ['code'=>'PPR20260708035','days_ago'=>45,'sup'=>'SUP0004','status'=>'draft',           'source'=>'offline',    'notes'=>'Draf restock kartu perdana XL & Tri - menunggu harga terbaru dari agen',                      'items'=>[['PRD00015',15,'pcs'],['PRD00016',10,'pcs']]],
            ['code'=>'PPR20260716036','days_ago'=>37,'sup'=>'SUP0002','status'=>'draft',           'source'=>'offline',    'notes'=>'Draf rencana beli pulsa & token listrik - akan dieksekusi setelah modal cair',                 'items'=>[['PRD00008',40,'trx'],['PRD00010',30,'trx']]],
            ['code'=>'PPR20260724037','days_ago'=>29,'sup'=>'SUP0005','status'=>'draft',           'source'=>'offline',    'notes'=>'Draf kulakan speaker bluetooth & earphone dari Vivan - proses approval',                      'items'=>[['PRD00037',5,'pcs'],['PRD00032',10,'pcs']]],
            ['code'=>'PPR20260805038','days_ago'=>17,'sup'=>'SUP0003','status'=>'draft',           'source'=>'marketplace','notes'=>'Draf beli IC charging & konektor dari Mitra Sparepart - menunggu budget'],
            ['code'=>'PPR20260813039','days_ago'=>9, 'sup'=>'SUP0001','status'=>'draft',           'source'=>'whatsapp',   'notes'=>'Draf order ring holder & iring magnet dari Roxy via WA - belum finalisasi'],
            ['code'=>'PPR20260819040','days_ago'=>3, 'sup'=>'SUP0002','status'=>'draft',           'source'=>'offline',    'notes'=>'Draf restock voucher XL & Smartfren untuk akhir bulan - dalam perencanaan'],

            // ─── CANCELLED (10 data) ─────────────────────────────────────────────────
            ['code'=>'PPR20260602041','days_ago'=>79,'sup'=>'SUP0001','status'=>'cancelled',       'source'=>'offline',    'notes'=>'Dibatalkan karena supplier Roxy Mas kehabisan stok holder motor aluminium',                   'items'=>[['PRD00028',15,'pcs']]],
            ['code'=>'PPR20260608042','days_ago'=>73,'sup'=>'SUP0005','status'=>'cancelled',       'source'=>'whatsapp',   'notes'=>'Order TWS via WA dibatalkan karena perubahan harga mendadak dari Vivan Dealer',               'items'=>[['PRD00033',10,'pcs']]],
            ['code'=>'PPR20260619043','days_ago'=>62,'sup'=>'SUP0003','status'=>'cancelled',       'source'=>'marketplace','notes'=>'Pemesanan LCD iPhone dari marketplace dibatalkan karena barang terindikasi KW',                'items'=>[['PRD00050',2,'pcs']]],
            ['code'=>'PPR20260628044','days_ago'=>55,'sup'=>'SUP0004','status'=>'cancelled',       'source'=>'offline',    'notes'=>'Order kartu perdana Smartfren dibatalkan karena agen tidak memiliki stok bulan ini',           'items'=>[['PRD00017',20,'pcs']]],
            ['code'=>'PPR20260706045','days_ago'=>47,'sup'=>'SUP0002','status'=>'cancelled',       'source'=>'offline',    'notes'=>'Rencana kulakan voucher 10GB dibatalkan karena harga naik melewati budget',                    'items'=>[['PRD00006',25,'voucher']]],
            ['code'=>'PPR20260714046','days_ago'=>39,'sup'=>'SUP0005','status'=>'cancelled',       'source'=>'whatsapp',   'notes'=>'Order powerbank 20k via WA dibatalkan karena stok Vivan kosong',                             'items'=>[['PRD00040',5,'pcs']]],
            ['code'=>'PPR20260721047','days_ago'=>32,'sup'=>'SUP0003','status'=>'cancelled',       'source'=>'marketplace','notes'=>'Beli backglass Samsung dari marketplace dibatalkan karena penjual offline',                   'items'=>[['PRD00046',3,'pcs']]],
            ['code'=>'PPR20260730048','days_ago'=>23,'sup'=>'SUP0001','status'=>'cancelled',       'source'=>'offline',    'notes'=>'Kulakan holder mobil dibatalkan karena keputusan owner fokus ke digital produk dulu',          'items'=>[['PRD00029',10,'pcs']]],
            ['code'=>'PPR20260807049','days_ago'=>15,'sup'=>'SUP0002','status'=>'cancelled',       'source'=>'offline',    'notes'=>'Restock token listrik PLN dibatalkan karena sistem distributor sedang down maintenance',       'items'=>[['PRD00010',50,'trx']]],
            ['code'=>'PPR20260817050','days_ago'=>5, 'sup'=>'SUP0005','status'=>'cancelled',       'source'=>'whatsapp',   'notes'=>'Order speaker BT via WA dibatalkan karena budget dialihkan untuk kebutuhan servis sparepart', 'items'=>[['PRD00037',4,'pcs']]],
        ];

        $supplierMap = $suppliers->keyBy('supplier_code');
        $productMap  = $products->keyBy('product_code');

        foreach ($purchases as $p) {
            $purchaseTime = now()->subDays($p['days_ago'])->setTime(rand(9, 17), rand(0, 59), 0);
            $supplier     = $supplierMap[$p['sup']] ?? $suppliers->first();

            $purchase = ProductPurchase::firstOrCreate(
                ['product_purchase_code' => $p['code']],
                [
                    'store_id'               => $store?->id,
                    'created_by'             => $user->id,
                    'purchase_date'          => $purchaseTime,
                    'estimated_arrival_date' => (clone $purchaseTime)->modify('+3 days'),
                    'total'                  => 0,
                    'status'                 => $p['status'],
                    'notes'                  => $p['notes'],
                    'partial_notes'          => $p['status'] === 'partial_received' ? 'Diterima sebagian oleh tim gudang, ada item dalam proses atau retur' : null,
                ]
            );

            if (!isset($p['items'])) continue;

            $total = 0;
            foreach ($p['items'] as [$prodCode, $qty]) {
                if (!isset($productMap[$prodCode])) continue;
                $product = $productMap[$prodCode];
                $subtotal = $product->purchase_price * $qty;
                $total += $subtotal;

                $isReceived     = $p['status'] === 'received';
                $isPartial      = $p['status'] === 'partial_received';
                $isCancelled    = $p['status'] === 'cancelled';

                $qtyReceived    = 0;
                $qtyRejected    = 0;
                $rejectionNotes = null;
                $isResolved     = false;

                if ($isReceived) {
                    $qtyReceived = $qty;
                    $isResolved  = true;
                } elseif ($isPartial) {
                    $qtyReceived = (int) max(1, floor($qty * 0.6));
                    if (rand(0, 1)) {
                        $qtyRejected    = 1;
                        $rejectionNotes = 'Kemasan penyok saat pengiriman ekspedisi';
                    }
                    $isResolved = false;
                } elseif ($isCancelled) {
                    $isResolved = true;
                }

                $source = $p['source'];
                $marketplace = $source === 'marketplace' ? 'Tokopedia Official' : null;

                ProductPurchaseItem::firstOrCreate(
                    [
                        'product_purchase_code' => $p['code'],
                        'product_code'          => $product->product_code,
                    ],
                    [
                        'source'            => $source,
                        'supplier_code'     => $supplier?->supplier_code,
                        'marketplace_name'  => $marketplace,
                        'is_resolved'       => $isResolved,
                        'quantity'          => $qty,
                        'quantity_received' => $qtyReceived,
                        'quantity_rejected' => $qtyRejected,
                        'rejection_notes'   => $rejectionNotes,
                        'purchase_price'    => $product->purchase_price,
                        'subtotal'          => $subtotal,
                    ]
                );
            }

            $purchase->update(['total' => $total]);
        }

        app()->forgetInstance('current_store');
    }
}
