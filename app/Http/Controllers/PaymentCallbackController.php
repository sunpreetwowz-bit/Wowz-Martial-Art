<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Online payment gateway calls this URL.
 * We check an optional secret signature, then mark paid/failed.
 */
class PaymentCallbackController extends Controller
{
    public function __invoke(Request $request, PaymentService $paymentService)
    {
        $data = $request->validate([
            'payment_uuid' => ['required', 'uuid', 'exists:payments,uuid'],
            'status' => ['required', 'string'],
            'transaction_id' => ['nullable', 'string', 'max:100'],
            'signature' => ['nullable', 'string', 'max:255'],
        ]);

        $this->checkSignature($data);

        $payment = Payment::query()->where('uuid', $data['payment_uuid'])->firstOrFail();
        $updated = $paymentService->handleCallback($payment, $data);

        return response()->json([
            'ok' => true,
            'payment_uuid' => $updated->uuid,
            'status' => $updated->status->value,
        ]);
    }

    /** If PAYMENT_CALLBACK_SECRET is set, require a matching HMAC signature. */
    protected function checkSignature(array $data): void
    {
        $secret = config('academy.payments.callback_secret');

        if (! $secret) {
            return;
        }

        $message = $data['payment_uuid'].'|'.$data['status'].'|'.($data['transaction_id'] ?? '');
        $expected = hash_hmac('sha256', $message, $secret);

        if (! hash_equals($expected, (string) ($data['signature'] ?? ''))) {
            throw new AccessDeniedHttpException('Invalid payment callback signature.');
        }
    }
}
