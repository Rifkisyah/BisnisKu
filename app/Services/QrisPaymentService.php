<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class QrisPaymentService
{
    protected $gateway;

    public function __construct()
    {
        $settings = PaymentSetting::getSettings();
        $this->gateway = new MidtransGatewayService();
    }

    public function createPayment(Transaction $transaction)
    {
        $settings = PaymentSetting::getSettings();
        if (!$settings->is_qris_active) {
            throw new Exception("QRIS tidak aktif");
        }

        if ($settings->qris_mode === 'manual') {
            return $this->createManualQrisPayment($transaction, $settings);
        } else {
            return $this->createDynamicPayment($transaction, $settings);
        }
    }

    protected function createManualQrisPayment(Transaction $transaction, PaymentSetting $settings)
    {
        $payment = Payment::create([
            'payment_code' => Payment::generateCode(),
            'transaction_code' => $transaction->transaction_code,
            'payment_method' => 'qris',
            'qris_mode' => 'manual',
            'amount' => $transaction->total,
            'status' => 'pending',
        ]);

        return [
            'success' => true,
            'qris_mode' => 'manual',
            'qris_url' => asset($settings->manual_qris_image),
            'payment_code' => $payment->payment_code
        ];
    }

    protected function createDynamicPayment(Transaction $transaction, PaymentSetting $settings)
    {
        $externalOrderId = 'TRX-' . $transaction->transaction_code . '-' . time();
        
        $params = [
            'transaction_details' => [
                'order_id' => $externalOrderId,
                'gross_amount' => (int) $transaction->total,
            ],
            'customer_details' => [
                'first_name' => 'Pelanggan',
                'last_name'  => 'BisnisKu',
            ]
        ];

        $response = $this->gateway->generateSnapUrl($params);

        if (!$response['success']) {
            $msg = $response['message'];
            if (strpos($msg, 'ServerKey/ClientKey is null') !== false || strpos($msg, 'server-key') !== false || strpos($msg, '401') !== false || strpos($msg, 'unauthorized') !== false) {
                throw new Exception("NOT_CONFIGURED:Kunci Server/Client Midtrans belum diatur atau tidak valid. Silakan atur terlebih dahulu di menu Pengaturan Pembayaran.");
            }
            throw new Exception("Gagal generate pembayaran: " . $msg);
        }

        $payment = Payment::create([
            'payment_code' => Payment::generateCode(),
            'transaction_code' => $transaction->transaction_code,
            'payment_method' => 'qris',
            'qris_mode' => 'dynamic', // Legacy flag to differentiate from manual
            'provider' => 'midtrans',
            'amount' => $transaction->total,
            'status' => 'pending',
            'external_order_id' => $externalOrderId,
        ]);

        return [
            'success' => true,
            'qris_mode' => 'dynamic',
            'snap_token' => $response['snap_token'],
            'payment_code' => $payment->payment_code,
            'external_order_id' => $externalOrderId
        ];
    }

    public function markAsPaid(Payment $payment, $confirmedBy = null, $callbackPayload = null)
    {
        return DB::transaction(function () use ($payment, $confirmedBy, $callbackPayload) {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'confirmed_by' => $confirmedBy,
                'callback_payload' => $callbackPayload
            ]);

            $transaction = $payment->transaction;
            $transaction->update(['status' => 'paid']);

            // Deduct stock now that the transaction is paid
            $stockService = new StockService();
            foreach ($transaction->items as $item) {
                $stockService->stockOut(
                    $item->product_code,
                    $item->quantity,
                    'transaction',
                    $transaction->transaction_code,
                    "Sale: {$transaction->transaction_code}"
                );
            }

            return true;
        });
    }

    public function cancelPayment(Payment $payment)
    {
        return DB::transaction(function () use ($payment) {
            $payment->update(['status' => 'cancelled']);
            $transaction = $payment->transaction;
            $transaction->update(['status' => 'cancelled']);
            
            // Stock is not restored because it was not deducted for a pending QRIS transaction

            return true;
        });
    }
}
