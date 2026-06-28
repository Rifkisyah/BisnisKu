<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $apiUrl = 'https://api.fonnte.com/send';
    protected ?string $token;

    public function __construct()
    {
        $this->token = config('services.fonnte.token');
    }

    /**
     * Send a WhatsApp message via Fonnte API.
     *
     * @param string $phone  Phone number with country code (e.g., 6281234567890)
     * @param string $message
     * @return array{success: bool, message: string, response: array|null}
     */
    public function send(string $phone, string $message): array
    {
        if (!$this->token) {
            Log::warning('WhatsAppService: Fonnte token is not configured.');
            return [
                'success' => false,
                'message' => 'WhatsApp API token belum dikonfigurasi.',
                'response' => null,
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->token,
            ])->post($this->apiUrl, [
                'target'  => $phone,
                'message' => $message,
            ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                return [
                    'success'  => true,
                    'message'  => 'Pesan berhasil dikirim.',
                    'response' => $body,
                ];
            }

            return [
                'success'  => false,
                'message'  => $body['reason'] ?? 'Gagal mengirim pesan.',
                'response' => $body,
            ];
        } catch (\Exception $e) {
            Log::error('WhatsAppService error: ' . $e->getMessage());
            return [
                'success'  => false,
                'message'  => 'Terjadi kesalahan saat menghubungi API WhatsApp.',
                'response' => null,
            ];
        }
    }

    /**
     * Format a phone number to Indonesian format with country code.
     * Handles 08xx → 628xx
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }
}
