<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Services\QrisPaymentService;
use Midtrans\Notification;
use Midtrans\Config;

class WebhookController extends Controller
{
    public function midtrans(Request $request)
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        
        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
        
        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;
        $fraud = $notification->fraud_status;
        
        // Find payment by external_order_id
        $payment = Payment::where('external_order_id', $orderId)->first();
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $service = new QrisPaymentService();

        if ($transaction == 'capture') {
            // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                } else {
                    $service->markAsPaid($payment, null, $notification->getResponse());
                    (new \App\Services\WhatsAppService())->sendReceipt($payment->transaction);
                }
            }
        } else if ($transaction == 'settlement' || $transaction == 'capture') {
            $service->markAsPaid($payment, null, $notification->getResponse());
            (new \App\Services\WhatsAppService())->sendReceipt($payment->transaction);
        } else if ($transaction == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
        } else if ($transaction == 'deny') {
            $service->cancelPayment($payment);
        } else if ($transaction == 'expire') {
            $service->cancelPayment($payment);
        } else if ($transaction == 'cancel') {
            $service->cancelPayment($payment);
        }

        return response()->json(['success' => true]);
    }
}
