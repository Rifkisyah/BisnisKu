<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Services\MidtransQrisService;
use App\Services\QrisPaymentService;
use Illuminate\Support\Facades\Log;

/**
 * WebhookController
 *
 * Menerima notifikasi dari Midtrans setelah transaksi QRIS berhasil/gagal.
 *
 * Route ini harus EXCLUDE dari CSRF (lihat bootstrap/app.php atau VerifyCsrfToken middleware).
 * URL webhook yang diinput ke dashboard Midtrans: https://yourdomain.com/webhook/midtrans
 */
class WebhookController extends Controller
{
    public function midtrans(Request $request)
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', [
            'order_id'           => $payload['order_id'] ?? null,
            'transaction_status' => $payload['transaction_status'] ?? null,
            'payment_type'       => $payload['payment_type'] ?? null,
        ]);

        // 1. Cari payment berdasarkan order_id
        $orderId = $payload['order_id'] ?? null;
        if (!$orderId) {
            return response()->json(['message' => 'Missing order_id'], 400);
        }

        $payment = Payment::where('external_order_id', $orderId)->first();
        if (!$payment) {
            Log::warning('Midtrans Webhook: Payment not found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // 2. Validasi signature Midtrans menggunakan Server Key yang benar (dari setting store)
        // Formula: SHA512(order_id + status_code + gross_amount + server_key)
        $settings = \App\Models\PaymentSetting::where('store_id', $payment->store_id)->first() ?? \App\Models\PaymentSetting::first();
        $gateway = \App\Services\MidtransQrisService::fromSettings($settings);
        
        if (!$gateway->validateCallback($payload, $request->headers->all())) {
            Log::warning('Midtrans Webhook: Invalid signature', ['order_id' => $orderId]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // 3. Proses berdasarkan status transaksi
        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus       = $payload['fraud_status'] ?? 'accept';
        $service           = new QrisPaymentService();

        switch ($transactionStatus) {
            case 'settlement':
                // QRIS berhasil dibayar
                if ($payment->status !== 'paid') {
                    $service->markAsPaid($payment, null, $payload);
                    Log::info('Midtrans Webhook: Payment settled', ['order_id' => $orderId]);

                    // Kirim notif WA jika ada kontak customer
                    $this->sendWhatsAppReceipt($payment);
                }
                break;

            case 'capture':
                // Kartu kredit: capture
                if ($fraudStatus === 'accept' && $payment->status !== 'paid') {
                    $service->markAsPaid($payment, null, $payload);
                    Log::info('Midtrans Webhook: Payment captured', ['order_id' => $orderId]);
                    $this->sendWhatsAppReceipt($payment);
                } elseif ($fraudStatus === 'challenge') {
                    Log::warning('Midtrans Webhook: Payment challenged by FDS', ['order_id' => $orderId]);
                }
                break;

            case 'pending':
                // Masih menunggu pembayaran — tidak perlu aksi
                Log::info('Midtrans Webhook: Payment pending', ['order_id' => $orderId]);
                break;

            case 'deny':
            case 'cancel':
            case 'failure':
                if ($payment->status === 'pending') {
                    $service->cancelPayment($payment);
                    Log::info('Midtrans Webhook: Payment cancelled/denied', [
                        'order_id' => $orderId,
                        'status'   => $transactionStatus,
                    ]);
                }
                break;

            case 'expire':
                if ($payment->status === 'pending') {
                    $payment->update(['status' => 'expired', 'callback_payload' => $payload]);
                    $payment->transaction?->update(['status' => 'cancelled']);
                    Log::info('Midtrans Webhook: Payment expired', ['order_id' => $orderId]);
                }
                break;

            default:
                Log::warning('Midtrans Webhook: Unknown status', [
                    'order_id' => $orderId,
                    'status'   => $transactionStatus,
                ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Kirim struk otomatis via WhatsApp setelah pembayaran berhasil.
     */
    protected function sendWhatsAppReceipt(Payment $payment): void
    {
        try {
            $transaction = $payment->transaction;
            if ($transaction && $transaction->customer_contact) {
                (new \App\Services\WhatsAppService())->sendReceipt($transaction);
            }
        } catch (\Exception $e) {
            Log::warning('Midtrans Webhook: WhatsApp send failed', ['error' => $e->getMessage()]);
        }
    }
}
