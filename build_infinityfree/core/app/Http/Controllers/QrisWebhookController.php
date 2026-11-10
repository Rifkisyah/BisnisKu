<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Services\DummyQrisGatewayService;
use App\Services\QrisPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QrisWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('QRIS Webhook Received', $request->all());

        $payload = $request->all();
        $headers = $request->headers->all();

        // Get external_order_id from payload (dummy example, varies by provider)
        $externalOrderId = $payload['external_order_id'] ?? null;
        if (!$externalOrderId) {
            return response()->json(['message' => 'Missing external_order_id'], 400);
        }

        $payment = Payment::where('external_order_id', $externalOrderId)
                          ->where('status', 'pending')
                          ->first();

        if (!$payment) {
            return response()->json(['message' => 'Payment not found or not pending'], 404);
        }

        // Validate signature
        $gateway = new DummyQrisGatewayService();
        if (!$gateway->validateCallback($payload, $headers)) {
            Log::warning('QRIS Webhook Invalid Signature', ['external_order_id' => $externalOrderId]);
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        // In real gateway, payload tells us if it's paid or failed. 
        // Dummy mock: assume paid.
        $status = $payload['status'] ?? 'paid';
        
        if ($status === 'paid') {
            $paymentService = new QrisPaymentService();
            $paymentService->markAsPaid($payment, null, $payload);
            Log::info('QRIS Webhook Processed Successfully', ['external_order_id' => $externalOrderId]);
        } else if ($status === 'failed' || $status === 'expired') {
            $payment->update(['status' => $status, 'callback_payload' => $payload]);
        }

        return response()->json(['message' => 'Success']);
    }
}
