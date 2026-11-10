<?php

namespace App\Services;

use App\Services\Contracts\PaymentGatewayInterface;

class DummyQrisGatewayService implements PaymentGatewayInterface
{
    public function generateQris(array $payload): array
    {
        // Mock generation
        return [
            'success' => true,
            'qris_url' => 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' . urlencode('DUMMY_QRIS_' . $payload['external_order_id']),
            'external_order_id' => $payload['external_order_id'],
            'message' => 'Success generated dummy QRIS'
        ];
    }

    public function checkStatus(string $externalOrderId): array
    {
        // Mock check status (always returns paid for testing)
        return [
            'status' => 'paid',
            'amount' => 0 // In real scenario we return the amount paid
        ];
    }

    public function validateCallback(array $payload, array $headers): bool
    {
        // Mock validation, always valid
        return true;
    }
}
