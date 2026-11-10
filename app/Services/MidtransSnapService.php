<?php

namespace App\Services;

use App\Models\PaymentSetting;
use Exception;
use Illuminate\Support\Facades\Log;

class MidtransSnapService
{
    public function __construct(PaymentSetting $settings)
    {
        if (empty($settings->server_key)) {
            throw new Exception("Midtrans Server Key belum dikonfigurasi. Harap lengkapi pengaturan pembayaran Anda.");
        }

        // Configure Midtrans library
        \Midtrans\Config::$serverKey = $settings->server_key;
        
        // Set Environment explicitly from database setting
        \Midtrans\Config::$isProduction = (bool) $settings->is_production;
        
        // Optional configuration
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    /**
     * Generate Midtrans Snap Token
     *
     * @param array $payload
     * @return string
     * @throws Exception
     */
    public function getSnapToken(array $payload): string
    {
        try {
            $snapToken = \Midtrans\Snap::getSnapToken($payload);
            return $snapToken;
        } catch (Exception $e) {
            Log::error('Midtrans Snap Exception: ' . $e->getMessage(), ['payload' => $payload]);
            throw new Exception("Gagal membuat token pembayaran Midtrans: " . $e->getMessage());
        }
    }
}
