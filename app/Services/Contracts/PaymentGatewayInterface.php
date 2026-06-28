<?php

namespace App\Services\Contracts;

interface PaymentGatewayInterface
{
    /**
     * Generate QRIS payment.
     *
     * @param array $payload
     * @return array ['success' => bool, 'qris_url' => string, 'external_order_id' => string, 'message' => string]
     */
    public function generateQris(array $payload): array;

    /**
     * Check payment status.
     *
     * @param string $externalOrderId
     * @return array ['status' => string 'paid'|'pending'|'failed', 'amount' => float]
     */
    public function checkStatus(string $externalOrderId): array;

    /**
     * Validate callback/webhook signature.
     *
     * @param array $payload
     * @param array $headers
     * @return bool
     */
    public function validateCallback(array $payload, array $headers): bool;
}
