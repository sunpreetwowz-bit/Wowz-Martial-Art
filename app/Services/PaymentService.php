<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\BeltTestApplication;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payments\ManualOnlineGateway;
use App\Services\Payments\OfflineCashGateway;
use App\Services\Payments\PaymentGatewayInterface;
use App\Support\AdminDashboardStats;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * Create payments, verify cash/UPI, and handle sandbox/online callbacks.
 */
class PaymentService
{
    protected $auditLogger;
    protected $notifications;

    public function __construct(
        AuditLogger $auditLogger,
        NotificationService $notifications,
    )
    {
        $this->auditLogger = $auditLogger;
        $this->notifications = $notifications;
    }

    /** Pick the gateway class for cash / upi / paytm. */
    public function gatewayFor(PaymentMethod $method)
    {
        if ($method === PaymentMethod::Cash) {
            return new OfflineCashGateway;
        }

        if ($method === PaymentMethod::Upi) {
            return new ManualOnlineGateway('upi');
        }

        return new ManualOnlineGateway('paytm');
    }

    public function createForApplication(
        BeltTestApplication $application,
        PaymentMethod $method,
        ?string $idempotencyKey = null,
    ) {
        $application->loadMissing('beltTest', 'student');
        $idempotencyKey = $idempotencyKey ?: (string) Str::uuid();

        return DB::transaction(function () use ($application, $method, $idempotencyKey) {
            // Same key = return the same payment (no duplicates)
            $existing = Payment::query()->where('idempotency_key', $idempotencyKey)->first();
            if ($existing) {
                return $existing;
            }

            $payment = Payment::query()->create([
                'belt_test_application_id' => $application->id,
                'student_id' => $application->student_id,
                'amount' => $application->beltTest->fee_amount,
                'currency' => $application->beltTest->currency ?? 'INR',
                'method' => $method,
                'status' => PaymentStatus::Pending,
                'idempotency_key' => $idempotencyKey,
                'gateway' => $method->value,
            ]);

            $result = $this->gatewayFor($method)->initiate($payment);

            $payment->update([
                'status' => PaymentStatus::from($result['status']),
                'gateway_payment_id' => $result['gateway_payment_id'] ?? null,
                'gateway_transaction_id' => $result['gateway_transaction_id'] ?? null,
                'gateway_response' => $result['gateway_response'] ?? null,
            ]);

            return $payment->fresh();
        });
    }

    /** Admin marks a pending payment as paid. */
    public function verifyOffline(Payment $payment, User $admin, ?string $notes = null)
    {
        if ($payment->status === PaymentStatus::Paid) {
            return $payment;
        }

        return DB::transaction(function () use ($payment, $admin, $notes) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === PaymentStatus::Paid) {
                return $locked->fresh(['application.beltTest', 'student.user']);
            }

            $locked->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
                'verified_by' => $admin->id,
                'verified_at' => now(),
                'notes' => $notes,
            ]);

            // Move application from payment_pending -> payment_verified
            $application = $locked->application;
            if ($application && $application->status === ApplicationStatus::PaymentPending) {
                $application->update([
                    'status' => ApplicationStatus::PaymentVerified,
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now(),
                ]);
            }

            $this->auditLogger->log(
                'payment.verified',
                $locked,
                ['status' => PaymentStatus::Pending->value],
                ['status' => PaymentStatus::Paid->value],
                $admin
            );

            AdminDashboardStats::forget();

            $locked = $locked->fresh(['application.beltTest', 'student.user']);
            $this->notifications->paymentVerified($locked);

            return $locked;
        });
    }

    /** Student finishes sandbox checkout (demo only). */
    public function completeSandbox(Payment $payment, bool $success)
    {
        if ($payment->method->isOffline()) {
            throw new InvalidArgumentException('Cash payments cannot use sandbox checkout.');
        }

        if ($payment->status === PaymentStatus::Paid) {
            return $payment;
        }

        return $this->handleCallback($payment, [
            'payment_uuid' => $payment->uuid,
            'status' => $success ? 'success' : 'failed',
            'transaction_id' => $success ? 'SANDBOX-TXN-'.Str::upper(Str::random(8)) : null,
        ]);
    }

    /**
     * Online gateway callback. Safe to call twice (idempotent).
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleCallback(Payment $payment, array $payload)
    {
        if ($payment->status === PaymentStatus::Paid) {
            return $payment;
        }

        $gateway = $this->gatewayFor($payment->method);

        // Gateway said payment failed
        if (! $gateway->verifyCallback($payment, $payload)) {
            $payment->update([
                'status' => PaymentStatus::Failed,
                'gateway_response' => array_merge($payment->gateway_response ?? [], ['callback' => $payload]),
            ]);

            $failed = $payment->fresh(['application.beltTest', 'student.user']);
            $this->notifications->paymentFailed($failed);

            return $failed;
        }

        return DB::transaction(function () use ($payment, $payload) {
            $locked = Payment::query()->whereKey($payment->id)->lockForUpdate()->firstOrFail();

            if ($locked->status === PaymentStatus::Paid) {
                return $locked;
            }

            $locked->update([
                'status' => PaymentStatus::Paid,
                'paid_at' => now(),
                'gateway_transaction_id' => $payload['transaction_id'] ?? $locked->gateway_transaction_id,
                'gateway_response' => array_merge($locked->gateway_response ?? [], ['callback' => $payload]),
            ]);

            $application = $locked->application;
            if ($application && $application->status === ApplicationStatus::PaymentPending) {
                $application->update(['status' => ApplicationStatus::PaymentVerified]);
            }

            $this->auditLogger->log(
                'payment.callback_verified',
                $locked,
                null,
                ['status' => PaymentStatus::Paid->value]
            );

            $locked = $locked->fresh(['application.beltTest', 'student.user']);
            $this->notifications->paymentVerified($locked);

            return $locked;
        });
    }
}
