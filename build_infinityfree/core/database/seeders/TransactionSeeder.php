<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    public function run(): void
    {
        $store = Store::where('slug', 'armal-cellular')->first();
        if ($store) {
            app()->instance('current_store', $store);
        }

        $kasirUser = User::whereHas('role', function ($q) { $q->where('name', 'kasir'); })
            ->where('store_id', $store?->id)->first();

        $products = Product::all()->keyBy('product_code');
        if (!$kasirUser || $products->isEmpty()) return;

        // 50 transaksi eksplisit mencakup semua status: paid (40), unpaid (5), cancelled (5)
        // Distribusi produk High/Medium/Low untuk K-Means Clustering
        $transactions = [
            // ─── STATUS: PAID (40 data) ───────────────────────────────────────────────
            ['code'=>'TRX202506010001','days_ago'=>85,'time'=>'09:15:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',3],['PRD00007',2]]],
            ['code'=>'TRX202506010002','days_ago'=>85,'time'=>'10:30:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00002',1],['PRD00025',2]]],
            ['code'=>'TRX202506020003','days_ago'=>84,'time'=>'08:45:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00008',1],['PRD00019',1]]],
            ['code'=>'TRX202506030004','days_ago'=>83,'time'=>'11:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',5],['PRD00004',2]]],
            ['code'=>'TRX202506040005','days_ago'=>82,'time'=>'13:20:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00007',1],['PRD00026',1]]],
            ['code'=>'TRX202506050006','days_ago'=>81,'time'=>'14:00:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00002',2]]],
            ['code'=>'TRX202506080007','days_ago'=>78,'time'=>'09:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',3],['PRD00027',1]]],
            ['code'=>'TRX202506090008','days_ago'=>77,'time'=>'10:15:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00004',2],['PRD00019',2]]],
            ['code'=>'TRX202506100009','days_ago'=>76,'time'=>'11:30:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00013',1],['PRD00025',1]]],
            ['code'=>'TRX202506110010','days_ago'=>75,'time'=>'14:45:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00032',1],['PRD00021',1]]],
            ['code'=>'TRX202506120011','days_ago'=>74,'time'=>'15:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00007',3],['PRD00008',1]]],
            ['code'=>'TRX202506150012','days_ago'=>71,'time'=>'08:30:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',4],['PRD00026',2]]],
            ['code'=>'TRX202506160013','days_ago'=>70,'time'=>'09:45:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00002',1],['PRD00014',1]]],
            ['code'=>'TRX202506170014','days_ago'=>69,'time'=>'13:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00025',3],['PRD00019',1]]],
            ['code'=>'TRX202506180015','days_ago'=>68,'time'=>'16:30:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00004',2],['PRD00027',2]]],
            ['code'=>'TRX202506190016','days_ago'=>67,'time'=>'10:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00008',2],['PRD00020',1]]],
            ['code'=>'TRX202506220017','days_ago'=>64,'time'=>'11:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',2],['PRD00007',1]]],
            ['code'=>'TRX202506230018','days_ago'=>63,'time'=>'12:15:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00026',1],['PRD00032',1]]],
            ['code'=>'TRX202506240019','days_ago'=>62,'time'=>'14:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00002',3],['PRD00025',1]]],
            ['code'=>'TRX202506250020','days_ago'=>61,'time'=>'09:30:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00019',2],['PRD00013',1]]],
            ['code'=>'TRX202507010021','days_ago'=>52,'time'=>'10:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',3],['PRD00004',1]]],
            ['code'=>'TRX202507030022','days_ago'=>50,'time'=>'11:30:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00008',1],['PRD00027',2]]],
            ['code'=>'TRX202507050023','days_ago'=>48,'time'=>'13:45:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00007',2],['PRD00026',1]]],
            ['code'=>'TRX202507080024','days_ago'=>45,'time'=>'15:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00002',2],['PRD00014',1]]],
            ['code'=>'TRX202507100025','days_ago'=>43,'time'=>'09:00:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00025',4],['PRD00019',1]]],
            ['code'=>'TRX202507120026','days_ago'=>41,'time'=>'10:30:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',5],['PRD00008',1]]],
            ['code'=>'TRX202507150027','days_ago'=>38,'time'=>'11:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00032',1],['PRD00021',2]]],
            ['code'=>'TRX202507170028','days_ago'=>36,'time'=>'14:15:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00004',2],['PRD00026',1]]],
            ['code'=>'TRX202507200029','days_ago'=>33,'time'=>'09:30:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00007',3],['PRD00027',1]]],
            ['code'=>'TRX202507220030','days_ago'=>31,'time'=>'10:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00002',1],['PRD00019',1]]],
            ['code'=>'TRX202507250031','days_ago'=>28,'time'=>'13:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',4],['PRD00025',2]]],
            ['code'=>'TRX202507280032','days_ago'=>25,'time'=>'15:30:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00008',2],['PRD00014',1]]],
            ['code'=>'TRX202508010033','days_ago'=>21,'time'=>'09:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00004',3],['PRD00026',1]]],
            ['code'=>'TRX202508050034','days_ago'=>17,'time'=>'10:45:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00007',4],['PRD00027',2]]],
            ['code'=>'TRX202508080035','days_ago'=>14,'time'=>'11:15:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00002',2],['PRD00032',1]]],
            ['code'=>'TRX202508100036','days_ago'=>12,'time'=>'13:30:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00001',3],['PRD00019',2]]],
            ['code'=>'TRX202508120037','days_ago'=>10,'time'=>'14:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00025',5],['PRD00013',1]]],
            ['code'=>'TRX202508150038','days_ago'=>7, 'time'=>'09:30:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00008',1],['PRD00026',3]]],
            ['code'=>'TRX202508180039','days_ago'=>4, 'time'=>'10:00:00','method'=>'qris',    'status'=>'paid',  'items'=>[['PRD00004',2],['PRD00021',1]]],
            ['code'=>'TRX202508200040','days_ago'=>2, 'time'=>'11:00:00','method'=>'cash',    'status'=>'paid',  'items'=>[['PRD00007',3],['PRD00027',1]]],

            // ─── STATUS: UNPAID (5 data) ──────────────────────────────────────────────
            ['code'=>'TRX202508150041','days_ago'=>7, 'time'=>'16:00:00','method'=>'debt',    'status'=>'unpaid','items'=>[['PRD00001',2],['PRD00025',1]]],
            ['code'=>'TRX202508170042','days_ago'=>5, 'time'=>'17:00:00','method'=>'debt',    'status'=>'unpaid','items'=>[['PRD00008',1],['PRD00019',1]]],
            ['code'=>'TRX202508190043','days_ago'=>3, 'time'=>'15:30:00','method'=>'debt',    'status'=>'unpaid','items'=>[['PRD00002',1],['PRD00026',2]]],
            ['code'=>'TRX202508200044','days_ago'=>2, 'time'=>'09:00:00','method'=>'debt',    'status'=>'unpaid','items'=>[['PRD00004',3]]],
            ['code'=>'TRX202508210045','days_ago'=>1, 'time'=>'14:00:00','method'=>'debt',    'status'=>'unpaid','items'=>[['PRD00007',2],['PRD00027',1]]],

            // ─── STATUS: CANCELLED (5 data) ───────────────────────────────────────────
            ['code'=>'TRX202507100046','days_ago'=>43,'time'=>'12:00:00','method'=>'cash',    'status'=>'cancelled','items'=>[['PRD00033',1],['PRD00038',1]]],
            ['code'=>'TRX202507200047','days_ago'=>33,'time'=>'13:00:00','method'=>'qris',    'status'=>'cancelled','items'=>[['PRD00043',1]]],
            ['code'=>'TRX202508010048','days_ago'=>21,'time'=>'15:00:00','method'=>'qris',    'status'=>'cancelled','items'=>[['PRD00029',1],['PRD00039',1]]],
            ['code'=>'TRX202508100049','days_ago'=>12,'time'=>'10:00:00','method'=>'cash',    'status'=>'cancelled','items'=>[['PRD00044',1]]],
            ['code'=>'TRX202508180050','days_ago'=>4, 'time'=>'16:30:00','method'=>'cash',    'status'=>'cancelled','items'=>[['PRD00028',1],['PRD00045',1]]],
        ];

        foreach ($transactions as $tx) {
            $txTime = now()->subDays($tx['days_ago'])->setTimeFromTimeString($tx['time']);

            $total = 0;
            $itemData = [];

            foreach ($tx['items'] as [$code, $qty]) {
                if (!isset($products[$code])) continue;
                $product = $products[$code];
                $subtotal = (int) $product->selling_price * $qty;
                $total += $subtotal;
                $itemData[] = [
                    'product_code' => $product->product_code,
                    'quantity'     => $qty,
                    'unit_price'   => $product->selling_price,
                    'subtotal'     => $subtotal,
                ];
            }

            if (empty($itemData)) continue;

            $isCancelled = $tx['status'] === 'cancelled';
            $isUnpaid    = $tx['status'] === 'unpaid';

            Transaction::updateOrCreate(
                ['transaction_code' => $tx['code']],
                [
                    'store_id'         => $store?->id,
                    'cashier_id'       => $kasirUser->id,
                    'transaction_date' => $txTime,
                    'subtotal'         => $total,
                    'discount'         => 0,
                    'total'            => $total,
                    'payment_method'   => $tx['method'],
                    'amount_paid'      => $isCancelled || $isUnpaid ? 0 : $total,
                    'change_amount'    => 0,
                    'status'           => $tx['status'],
                ]
            );

            if (!$isCancelled) {
                foreach ($itemData as $item) {
                    TransactionItem::firstOrCreate(
                        [
                            'transaction_code' => $tx['code'],
                            'product_code'     => $item['product_code'],
                        ],
                        [
                            'quantity'         => $item['quantity'],
                            'unit_price'       => $item['unit_price'],
                            'discount_product' => 0,
                            'subtotal'         => $item['subtotal'],
                        ]
                    );
                }
            }
        }

        app()->forgetInstance('current_store');
    }
}
