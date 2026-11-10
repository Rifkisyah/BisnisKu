<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\MidtransQrisService;
use App\Services\QrisPaymentService;
use Illuminate\Http\Request;

class PosPaymentController extends Controller
{
    /**
     * Kasir konfirmasi manual bahwa QRIS statis sudah dibayar customer.
     */
    public function confirmManualQris(Request $request, $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)->firstOrFail();

        if ($payment->status !== 'pending' || $payment->qris_mode !== 'manual') {
            return response()->json(['message' => 'Pembayaran tidak dapat dikonfirmasi'], 400);
        }

        $service = new QrisPaymentService();
        $service->markAsPaid($payment, auth()->id());

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi',
        ]);
    }

    /**
     * Batalkan pembayaran yang masih pending.
     */
    public function cancelPayment(Request $request, $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)->firstOrFail();

        if ($payment->status !== 'pending') {
            return response()->json(['message' => 'Hanya pembayaran pending yang dapat dibatalkan'], 400);
        }

        $service = new QrisPaymentService();
        $service->cancelPayment($payment);

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran dibatalkan',
        ]);
    }

    /**
     * Polling status QRIS dinamis dari frontend.
     * Mengecek langsung ke Midtrans API agar real-time.
     */
    public function checkDynamicQrisStatus(Request $request, $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)->firstOrFail();

        if ($payment->qris_mode !== 'dynamic') {
            return response()->json(['message' => 'Bukan pembayaran QRIS dinamis'], 400);
        }

        // Sudah diproses webhook sebelumnya
        if ($payment->status === 'paid') {
            return response()->json([
                'success' => true,
                'status'  => 'paid',
                'message' => 'Pembayaran sudah lunas',
            ]);
        }

        // Cek apakah QRIS sudah expired
        if ($payment->expiry_time && now()->gt($payment->expiry_time)) {
            if ($payment->status === 'pending') {
                $service = new QrisPaymentService();
                $service->cancelPayment($payment);
            }
            return response()->json([
                'success' => true,
                'status'  => 'expired',
                'message' => 'QRIS sudah kedaluwarsa. Silakan buat transaksi baru.',
            ]);
        }

        // Cek status real-time ke Midtrans
        $settings = \App\Models\PaymentSetting::where('store_id', $payment->store_id)->first() ?? \App\Models\PaymentSetting::first();
        $gateway = \App\Services\MidtransQrisService::fromSettings($settings);
        $result  = $gateway->checkStatus($payment->external_order_id);

        if ($result['status'] === 'paid') {
            $service = new QrisPaymentService();
            $service->markAsPaid($payment, null, $result['raw'] ?? $result);

            return response()->json([
                'success' => true,
                'status'  => 'paid',
                'message' => 'Pembayaran berhasil diverifikasi',
            ]);
        }

        if (in_array($result['status'], ['failed', 'expired'])) {
            $service = new QrisPaymentService();
            $service->cancelPayment($payment);

            return response()->json([
                'success' => true,
                'status'  => $result['status'],
                'message' => 'Pembayaran ' . $result['status'],
            ]);
        }

        // Masih pending
        $remaining = null;
        if ($payment->expiry_time) {
            $remaining = max(0, now()->diffInSeconds($payment->expiry_time, false));
        }

        return response()->json([
            'success'           => true,
            'status'            => 'pending',
            'message'           => 'Menunggu pembayaran...',
            'remaining_seconds' => $remaining,
        ]);
    }
}
