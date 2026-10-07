<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;

class OfflineCashGateway implements PaymentGatewayInterface
{
    public function name()
    {
        return 'cash';
    }

    public function initiate(Payment $payment, array $meta = [])
    {
        return [
            'status' => PaymentStatus::Pending->value,
            'gateway_payment_id' => null,
            'gateway_transaction_id' => null,
            'gateway_response' => [
                'message' => 'Cash selected. Awaiting admin verification.',
            ],
        ];
    }

    public function verifyCallback(Payment $payment, array $payload)
    {
        return false;
    }
}
