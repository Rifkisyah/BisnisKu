<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Services\StockService;

class QrisPaymentService
{
    protected $gateway;

    public function __construct()
    {
        // Gateway akan di-resolve per request berdasarkan setting
    }

    /**
     * Buat pembayaran QRIS (manual atau dinamis) berdasarkan setting toko.
     */
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

    /**
     * Mode Manual: tampilkan QR statis yang sudah diupload owner.
     * Pembayaran dikonfirmasi manual oleh kasir.
     */
    protected function createManualQrisPayment(Transaction $transaction, PaymentSetting $settings)
    {
        $payment = Payment::create([
            'payment_code'     => Payment::generateCode(),
            'transaction_code' => $transaction->transaction_code,
            'payment_method'   => 'qris',
            'qris_mode'        => 'manual',
            'amount'           => $transaction->total,
            'status'           => 'pending',
        ]);

        return [
            'success'      => true,
            'qris_mode'    => 'manual',
            'qris_url'     => asset($settings->manual_qris_image),
            'payment_code' => $payment->payment_code,
        ];
    }

    /**
     * Mode Dinamis: generate QR unik per transaksi via Midtrans Charge API.
     * QR mengandung nominal yang tepat, pembayaran dikonfirmasi otomatis via webhook.
     */
    protected function createDynamicPayment(Transaction $transaction, PaymentSetting $settings)
    {
        $externalOrderId = 'TRX-' . $transaction->transaction_code . '-' . time();

        // Gunakan per-store server_key jika sudah diset, fallback ke .env
        $gateway = MidtransQrisService::fromSettings($settings);

        $response = $gateway->generateQris([
            'external_order_id' => $externalOrderId,
            'amount'            => $transaction->total,
            'acquirer'          => $settings->qris_acquirer ?? 'gopay',
        ]);

        if (!$response['success']) {
            throw new Exception("Gagal generate QRIS: " . $response['message']);
        }

        $payment = Payment::create([
            'payment_code'      => Payment::generateCode(),
            'transaction_code'  => $transaction->transaction_code,
            'payment_method'    => 'qris',
            'qris_mode'         => 'dynamic',
            'provider'          => 'midtrans',
            'amount'            => $transaction->total,
            'status'            => 'pending',
            'external_order_id' => $externalOrderId,
            'qr_string'         => $response['qr_string'],
            'expiry_time'       => $response['expiry_time'],
        ]);

        return [
            'success'           => true,
            'qris_mode'         => 'dynamic',
            'qris_url'          => $response['qris_url'],
            'qr_string'         => $response['qr_string'],
            'expiry_time'       => $response['expiry_time'],
            'payment_code'      => $payment->payment_code,
            'external_order_id' => $externalOrderId,
        ];
    }

    /**
     * Tandai pembayaran sebagai lunas dan kurangi stok.
     * Dipanggil oleh webhook atau konfirmasi manual kasir.
     */
    public function markAsPaid(Payment $payment, $confirmedBy = null, $callbackPayload = null)
    {
        return DB::transaction(function () use ($payment, $confirmedBy, $callbackPayload) {
            // Idempotency: jika sudah paid, skip
            if ($payment->status === 'paid') {
                return true;
            }

            $payment->update([
                'status'           => 'paid',
                'paid_at'          => now(),
                'confirmed_by'     => $confirmedBy,
                'callback_payload' => $callbackPayload,
            ]);

            $transaction = $payment->transaction;
            $transaction->update(['status' => 'paid']);

            // Kurangi stok setelah pembayaran dikonfirmasi
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

    /**
     * Batalkan pembayaran.
     */
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
