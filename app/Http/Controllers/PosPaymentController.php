<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\DummyQrisGatewayService;
use App\Services\QrisPaymentService;
use Illuminate\Http\Request;

class PosPaymentController extends Controller
{
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
            'message' => 'Pembayaran berhasil dikonfirmasi'
        ]);
    }

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
            'message' => 'Pembayaran dibatalkan'
        ]);
    }

    public function checkDynamicQrisStatus(Request $request, $paymentCode)
    {
        $payment = Payment::where('payment_code', $paymentCode)->firstOrFail();
        
        if ($payment->qris_mode !== 'dynamic') {
            return response()->json(['message' => 'Bukan pembayaran QRIS dinamis'], 400);
        }

        // If webhook already processed it:
        if ($payment->status === 'paid') {
            return response()->json([
                'success' => true,
                'status' => 'paid',
                'message' => 'Pembayaran sudah lunas'
            ]);
        }

        $gateway = new DummyQrisGatewayService();
        $result = $gateway->checkStatus($payment->external_order_id);

        if ($result['status'] === 'paid') {
            $service = new QrisPaymentService();
            $service->markAsPaid($payment, null, $result);

            return response()->json([
                'success' => true,
                'status' => 'paid',
                'message' => 'Pembayaran berhasil diverifikasi via API'
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => $result['status'],
            'message' => 'Pembayaran masih tertunda'
        ]);
    }
}
