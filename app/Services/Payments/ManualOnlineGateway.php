<?php

namespace App\Services\Payments;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Str;

/**
 * Phase-1 stub for UPI / Paytm.
 * Creates a pending online payment that admin can verify,
 * or a future real gateway can confirm via callback.
 */
class ManualOnlineGateway implements PaymentGatewayInterface
{
    protected $gatewayName;

    public function __construct(string $gatewayName = 'upi')
    {
        $this->gatewayName = $gatewayName;
    }

    public function name()
    {
        return $this->gatewayName;
    }

    public function initiate(Payment $payment, array $meta = [])
    {
        return [
            'status' => PaymentStatus::Pending->value,
            'gateway_payment_id' => 'SANDBOX-'.Str::upper(Str::random(10)),
            'gateway_transaction_id' => null,
            'gateway_response' => [
                'message' => 'Sandbox checkout ready. Complete payment on the academy checkout page.',
                'reference' => $payment->uuid,
                'provider' => $this->gatewayName,
                'mode' => 'sandbox',
                'checkout_url' => route('student.payments.checkout', $payment, false),
            ],
        ];
    }

    public function verifyCallback(Payment $payment, array $payload)
    {
        // Real providers will validate signatures here.
        return ($payload['status'] ?? null) === 'success'
            && ($payload['payment_uuid'] ?? null) === $payment->uuid;
    }
}
