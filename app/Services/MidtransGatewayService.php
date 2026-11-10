<?php

namespace App\Services;

use Exception;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransGatewayService
{
    public function __construct()
    {
        $settings = \App\Models\PaymentSetting::getSettings();
        
        Config::$serverKey = $settings->server_key ?: config('midtrans.server_key');
        // Force to string or bool based on what midtrans lib expects. Usually bool.
        Config::$isProduction = $settings->server_key ? (bool)$settings->is_production : config('midtrans.is_production');
        
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function generateSnapUrl($params)
    {
        try {
            // getSnapToken internally creates the Midtrans transaction.
            // Do NOT also call Snap::createTransaction with the same order_id
            // as that would fail with "duplicate order_id" errors.
            $snapToken = Snap::getSnapToken($params);
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => null, // Not used; Snap popup handles the flow
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
