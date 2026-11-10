<?php

namespace App\Services;

use App\Services\Contracts\PaymentGatewayInterface;
use Illuminate\Support\Facades\Log;

/**
 * MidtransQrisService
 *
 * Menggunakan Midtrans Charge API (bukan Snap) untuk menghasilkan
 * QRIS dinamis yang benar-benar bisa dibayar.
 *
 * Dokumentasi: https://docs.midtrans.com/reference/qris
 *
 * Setup:
 * 1. Aktifkan QRIS di dashboard Midtrans → Settings → Payment Methods
 * 2. Isi MIDTRANS_SERVER_KEY di .env
 * 3. Set MIDTRANS_IS_PRODUCTION=false untuk sandbox / true untuk production
 */
class MidtransQrisService implements PaymentGatewayInterface
{
    protected string $serverKey;
    protected bool $isProduction;
    protected string $baseUrl;

    public function __construct(?string $serverKey = null, ?bool $isProduction = null)
    {
        $this->serverKey = $serverKey;
        
        if (empty($this->serverKey)) {
            throw new \RuntimeException("Midtrans Server Key belum dikonfigurasi. Harap lengkapi pengaturan pembayaran Anda.");
        }
        
        // Auto-detect Sandbox/Production from Server Key Prefix (Sandbox keys start with SB-)
        $this->isProduction = $isProduction ?? (!str_starts_with($this->serverKey, 'SB-'));
        
        $this->baseUrl = $this->isProduction
            ? 'https://api.midtrans.com/v2'
            : 'https://api.sandbox.midtrans.com/v2';
    }

    /**
     * Buat instance dengan menggunakan key dari PaymentSetting toko saat ini.
     * Tidak lagi fallback ke .env.
     */
    public static function fromSettings(\App\Models\PaymentSetting $settings): static
    {
        return new static(
            serverKey: $settings->server_key
        );
    }

    /**
     * Generate QRIS dinamis via Midtrans Charge API.
     *
     * @param array $payload ['external_order_id', 'amount', 'acquirer' (optional)]
     * @return array ['success', 'qr_string', 'qris_url', 'external_order_id', 'expiry_time', 'message']
     */
    public function generateQris(array $payload): array
    {
        $orderId  = $payload['external_order_id'];
        $amount   = (int) $payload['amount'];
        $acquirer = $payload['acquirer'] ?? 'gopay'; // gopay, airpay shopee

        $body = [
            'payment_type' => 'qris',
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $amount,
            ],
            'qris' => [
                'acquirer' => $acquirer,
            ],
        ];

        try {
            $response = $this->request('POST', '/charge', $body);

            // status_code 201 = created (pending)
            if (!in_array($response['status_code'] ?? '', ['200', '201'])) {
                $statusMessage = $response['status_message'] ?? 'Unknown error dari Midtrans';
                Log::error('Midtrans QRIS generate failed', [
                    'order_id' => $orderId,
                    'response' => $response,
                ]);
                return [
                    'success' => false,
                    'message' => $statusMessage,
                ];
            }

            $qrString   = $response['qr_string'] ?? '';
            $expiryTime = $response['expiry_time'] ?? now()->addMinutes(15)->toDateTimeString();

            // qris_url: gunakan QR server publik untuk render QR image dari qr_string
            $qrisUrl = 'https://api.qrserver.com/v1/create-qr-code/'
                . '?size=350x350&margin=10&ecc=M&data=' . urlencode($qrString);

            return [
                'success'          => true,
                'qr_string'        => $qrString,
                'qris_url'         => $qrisUrl,
                'external_order_id'=> $orderId,
                'expiry_time'      => $expiryTime,
                'acquirer'         => $response['acquirer'] ?? $acquirer,
                'message'          => $response['status_message'] ?? 'QRIS berhasil dibuat',
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans QRIS generate exception', [
                'order_id'  => $orderId,
                'error'     => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => 'Gagal menghubungi Midtrans: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Cek status pembayaran via Midtrans Status API.
     *
     * @param string $externalOrderId
     * @return array ['status' => 'paid'|'pending'|'failed'|'expired', 'amount']
     */
    public function checkStatus(string $externalOrderId): array
    {
        try {
            $response = $this->request('GET', "/{$externalOrderId}/status");

            $transactionStatus = $response['transaction_status'] ?? 'pending';
            $fraudStatus       = $response['fraud_status'] ?? 'accept';
            $grossAmount       = (float) ($response['gross_amount'] ?? 0);

            $status = match (true) {
                $transactionStatus === 'settlement'                                      => 'paid',
                $transactionStatus === 'capture' && $fraudStatus === 'accept'            => 'paid',
                in_array($transactionStatus, ['deny', 'cancel', 'failure'])              => 'failed',
                in_array($transactionStatus, ['expire'])                                 => 'expired',
                default                                                                  => 'pending',
            };

            return [
                'status' => $status,
                'amount' => $grossAmount,
                'raw'    => $response,
            ];
        } catch (\Exception $e) {
            Log::error('Midtrans checkStatus exception', [
                'order_id' => $externalOrderId,
                'error'    => $e->getMessage(),
            ]);
            return [
                'status' => 'pending',
                'amount' => 0,
            ];
        }
    }

    /**
     * Validasi signature webhook dari Midtrans.
     *
     * Formula: SHA512(order_id + status_code + gross_amount + server_key)
     *
     * @param array $payload  Isi notification dari Midtrans
     * @param array $headers  HTTP headers (tidak digunakan Midtrans, tapi wajib ada di interface)
     * @return bool
     */
    public function validateCallback(array $payload, array $headers): bool
    {
        $orderId      = $payload['order_id']      ?? '';
        $statusCode   = $payload['status_code']   ?? '';
        $grossAmount  = $payload['gross_amount']  ?? '';
        $signatureKey = $payload['signature_key'] ?? '';

        if (empty($signatureKey)) {
            Log::warning('Midtrans webhook: missing signature_key', ['order_id' => $orderId]);
            return false;
        }

        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);

        if (!hash_equals($expected, $signatureKey)) {
            Log::warning('Midtrans webhook: invalid signature', [
                'order_id' => $orderId,
                'expected' => substr($expected, 0, 16) . '...',
                'received' => substr($signatureKey, 0, 16) . '...',
            ]);
            return false;
        }

        return true;
    }

    /**
     * Helper: kirim HTTP request ke Midtrans API.
     */
    protected function request(string $method, string $path, array $body = []): array
    {
        $url = $this->baseUrl . $path;
        $auth = base64_encode($this->serverKey . ':');

        $options = [
            'http' => [
                'header'        => "Content-Type: application/json\r\nAuthorization: Basic {$auth}\r\nAccept: application/json\r\n",
                'method'        => $method,
                'ignore_errors' => true,
                'timeout'       => 30,
            ],
        ];

        if (!empty($body)) {
            $options['http']['content'] = json_encode($body);
        }

        $context  = stream_context_create($options);
        $rawResponse = file_get_contents($url, false, $context);

        if ($rawResponse === false) {
            throw new \RuntimeException("Tidak dapat terhubung ke Midtrans API: {$url}");
        }

        $decoded = json_decode($rawResponse, true);

        if (!is_array($decoded)) {
            throw new \RuntimeException("Response Midtrans bukan JSON valid: " . substr($rawResponse, 0, 200));
        }

        return $decoded;
    }
}
