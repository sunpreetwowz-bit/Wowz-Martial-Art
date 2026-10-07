<?php

namespace App\Services\Payments;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    public function name();

    /** Start a payment and return status details. */
    public function initiate(Payment $payment, array $meta = []);

    /** Check a gateway callback payload. */
    public function verifyCallback(Payment $payment, array $payload);
}
