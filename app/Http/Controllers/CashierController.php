<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierController extends Controller
{
    public function index()
    {
        $products = Product::available()
            ->where('stock', '>', 0)->where('type', '!=', 'sparepart')
            ->whereHas('category', function($q) {
                $q->where('type', 'product')->where('slug', '!=', 'sparepart');
            })
            ->with('category')
            ->orderBy('name')
            ->get();

        $categories = \App\Models\Category::where('type', 'product')->where('slug', '!=', 'sparepart')->orderBy('name')->get();

        return view('cashier.index', compact('products', 'categories'));
    }

    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        $products = Product::available()
            ->where('stock', '>', 0)->where('type', '!=', 'sparepart')
            ->whereHas('category', function($q) {
                $q->where('type', 'product')->where('slug', '!=', 'sparepart');
            })
            ->where(fn($q) => $q->where('name', 'like', "%{$search}%")->orWhere('product_code', 'like', "%{$search}%"))
            ->with('category')
            ->limit(20)
            ->get();

        return response()->json($products);
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items'              => 'required|array|min:1',
            'items.*.product_code' => 'required|exists:products,product_code',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'payment_method'     => 'required|in:cash,qris,debt',
            'amount_paid'        => 'required|numeric|min:0',
            'debtor_name'        => 'required_if:payment_method,debt|nullable|string',
            'debtor_contact'     => 'nullable|string',
            'customer_contact'   => 'nullable|string',
            'due_date'           => 'nullable|date',
            'notes'              => 'nullable|string',
        ]);

        $stockService = new StockService();

        try {
            $transactionData = DB::transaction(function () use ($validated, $stockService) {
                $subtotal = 0;
                $items    = [];

                foreach ($validated['items'] as $item) {
                    $product = Product::findOrFail($item['product_code']);

                    if ($product->stock < $item['quantity']) {
                        throw new \Exception("Stok {$product->name} tidak mencukupi. Tersedia: {$product->stock}");
                    }

                    $itemSubtotal = $item['unit_price'] * $item['quantity'];
                    $subtotal    += $itemSubtotal;

                    $items[] = [
                        'product'    => $product,
                        'quantity'   => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'subtotal'   => $itemSubtotal,
                    ];
                }

                $discount  = $validated['discount'] ?? 0;
                $total     = $subtotal - $discount;
                $amountPaid = $validated['amount_paid'];
                $change    = max(0, $amountPaid - $total);

                if ($validated['payment_method'] !== 'debt' && $amountPaid < $total) {
                    throw new \Exception('Jumlah pembayaran kurang dari total.');
                }

                $transactionCode = Transaction::generateCode();
                $isQris = $validated['payment_method'] === 'qris';

                $transaction = Transaction::create([
                    'transaction_code' => $transactionCode,
                    'cashier_id'       => auth()->id(),
                    'transaction_date' => now(),
                    'subtotal'         => $subtotal,
                    'discount'         => $discount,
                    'total'            => $total,
                    'payment_method'   => $validated['payment_method'],
                    'amount_paid'      => $amountPaid,
                    'change_amount'    => $change,
                    'status'           => $isQris ? 'unpaid' : 'paid',
                    'notes'            => $validated['notes'] ?? null,
                ]);

                if ($validated['payment_method'] === 'debt') {
                    \App\Models\Debt::create([
                        'debt_code'        => \App\Models\Debt::generateCode(),
                        'debtor_name'      => $validated['debtor_name'],
                        'debtor_contact'   => $validated['debtor_contact'] ?? null,
                        'total_amount'     => $total,
                        'paid_amount'      => $amountPaid,
                        'remaining_amount' => $total - $amountPaid,
                        'debt_date'        => now(),
                        'due_date'         => $validated['due_date'] ?? null,
                        'status'           => $amountPaid > 0 ? 'partial' : 'unpaid',
                        'transaction_code' => $transactionCode,
                    ]);
                    $transaction->update(['status' => 'paid']); // Transaction itself is considered paid by the debt agreement
                }

                foreach ($items as $item) {
                    TransactionItem::create([
                        'transaction_code' => $transactionCode,
                        'product_code'     => $item['product']->product_code,
                        'quantity'         => $item['quantity'],
                        'unit_price'       => $item['unit_price'],
                        'subtotal'         => $item['subtotal'],
                        'discount_product' => 0,
                    ]);

                    // Only reduce stock if it's NOT QRIS (because QRIS reduces stock after payment callback)
                    if (!$isQris) {
                        $stockService->stockOut(
                            $item['product']->product_code,
                            $item['quantity'],
                            'transaction',
                            $transactionCode,
                            "Sale: {$transactionCode}"
                        );
                    }
                }

                // If QRIS, trigger QrisPaymentService
                $qrisData = null;
                if ($isQris) {
                    $qrisService = new \App\Services\QrisPaymentService();
                    $qrisData = $qrisService->createPayment($transaction);
                }

                return [
                    'transaction'      => $transaction,
                    'items'            => $items,
                    'isQris'           => $isQris,
                    'qrisData'         => $qrisData,
                ];
            });
            
            $result = $transactionData;
            $transaction = $result['transaction'];
            
            // Send WA Receipt if contact is provided
            $waSent = false;
            $waError = null;
            if (!empty($validated['customer_contact'])) {
                $waService = new \App\Services\WhatsAppService();
                $phone = \App\Services\WhatsAppService::formatPhone($validated['customer_contact']);
                
                $storeName = \App\Models\Setting::get('store_name', 'Toko Kami');
                $receiptUrl = route('transactions.receipt', $transaction->transaction_code);
                
                $message = "Halo, ini struk belanja Anda di *{$storeName}*.\n\n";
                $message .= "No. Transaksi: {$transaction->transaction_code}\n";
                foreach ($result['items'] as $item) {
                    $message .= "- {$item['product']->name}: {$item['quantity']} x Rp " . number_format($item['unit_price'], 0, ',', '.') . "\n";
                }
                $message .= "\nTotal: *Rp " . number_format($transaction->total, 0, ',', '.') . "*\n";
                $message .= "Metode: " . strtoupper($transaction->payment_method) . "\n\n";
                $message .= "Lihat struk lengkap: {$receiptUrl}\n\nTerima kasih atas kunjungan Anda!";
                
                $waResult = $waService->send($phone, $message);
                $waSent = $waResult['success'];
                if (!$waSent) $waError = $waResult['message'];
            }

            return response()->json([
                'success'          => true,
                'transaction_code' => $transaction->transaction_code,
                'total'            => $transaction->total,
                'receipt_url'      => route('transactions.receipt', $transaction),
                'message'          => __('messages.transaction_success'),
                'qris_data'        => $result['qrisData'],
                'wa_sent'          => $waSent,
                'wa_error'         => $waError
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
