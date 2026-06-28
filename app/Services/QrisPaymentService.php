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
        // Here we can switch to real gateway like Midtrans based on settings
        // For now, use Dummy
        $this->gateway = new DummyQrisGatewayService();
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
            return $this->createDynamicQrisPayment($transaction, $settings);
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

    protected function createDynamicQrisPayment(Transaction $transaction, PaymentSetting $settings)
    {
        $externalOrderId = 'QRIS-' . $transaction->transaction_code . '-' . time();
        
        $response = $this->gateway->generateQris([
            'external_order_id' => $externalOrderId,
            'amount' => $transaction->total,
        ]);

        if (!$response['success']) {
            throw new Exception("Gagal generate QRIS: " . $response['message']);
        }

        $payment = Payment::create([
            'payment_code' => Payment::generateCode(),
            'transaction_code' => $transaction->transaction_code,
            'payment_method' => 'qris',
            'qris_mode' => 'dynamic',
            'provider' => $settings->qris_provider,
            'amount' => $transaction->total,
            'status' => 'pending',
            'external_order_id' => $externalOrderId,
        ]);

        return [
            'success' => true,
            'qris_mode' => 'dynamic',
            'qris_url' => $response['qris_url'],
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

            // Decrease stock
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
            return true;
        });
    }
}
