<?php

namespace App\Services;

use Exception;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransGatewayService
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function generateSnapUrl($params)
    {
        try {
            $snapToken = Snap::getSnapToken($params);
            $redirectUrl = Snap::createTransaction($params)->redirect_url;
            return [
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => $redirectUrl,
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
