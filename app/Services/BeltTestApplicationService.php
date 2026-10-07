<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\PaymentMethod;
use App\Exceptions\BeltTestAccessException;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\Student;
use App\Models\User;
use App\Support\AdminDashboardStats;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Student applies to a belt test. Admin can update the application status.
 */
class BeltTestApplicationService
{
    protected $accessService;
    protected $paymentService;
    protected $auditLogger;
    protected $notifications;

    public function __construct(
        BeltTestAccessService $accessService,
        PaymentService $paymentService,
        AuditLogger $auditLogger,
        NotificationService $notifications,
    )
    {
        $this->accessService = $accessService;
        $this->paymentService = $paymentService;
        $this->auditLogger = $auditLogger;
        $this->notifications = $notifications;
    }

    /**
     * Student submits one application for a belt test.
     *
     * @param  array<string, mixed>  $formData
     */
    public function submit(
        Student $student,
        BeltTest $beltTest,
        $paymentMethod,
        array $formData = [],
        $idempotencyKey = null
    ) {
        // Form sends "cash" / "upi" as text — convert to enum
        if (is_string($paymentMethod)) {
            $paymentMethod = PaymentMethod::from($paymentMethod);
        }

        $this->accessService->assertCanApply($student, $beltTest);

        try {
            return DB::transaction(function () use ($student, $beltTest, $paymentMethod, $formData, $idempotencyKey) {
                $hasFee = ((float) $beltTest->fee_amount) > 0;
                $status = $hasFee
                    ? ApplicationStatus::PaymentPending
                    : ApplicationStatus::Submitted;

                $application = BeltTestApplication::query()->create([
                    'belt_test_id' => $beltTest->id,
                    'student_id' => $student->id,
                    'current_belt_id' => $student->current_belt_id,
                    'target_belt_id' => $beltTest->target_belt_id,
                    'form_data' => $formData,
                    'status' => $status,
                    'submitted_at' => now(),
                ]);

                if ($hasFee) {
                    $this->paymentService->createForApplication(
                        $application,
                        $paymentMethod,
                        $idempotencyKey
                    );
                }

                $this->auditLogger->log(
                    'belt_test_application.submitted',
                    $application,
                    null,
                    [
                        'belt_test_id' => $beltTest->id,
                        'student_id' => $student->id,
                        'status' => $status->value,
                        'payment_method' => $paymentMethod->value,
                    ]
                );

                $application = $application->load([
                    'beltTest',
                    'targetBelt',
                    'currentBelt',
                    'latestPayment',
                    'student.user',
                ]);

                AdminDashboardStats::forget();
                $this->notifications->applicationSubmitted($application);

                return $application;
            });
        } catch (QueryException $e) {
            // Unique index means the student already applied
            if (str_contains(strtolower($e->getMessage()), 'unique')) {
                throw BeltTestAccessException::alreadySubmitted();
            }

            throw $e;
        }
    }

    /**
     * Admin updates application status from the review form.
     * $status is a simple string like "accepted" or "rejected".
     */
    public function review(
        BeltTestApplication $application,
        string $status,
        User $admin,
        ?string $notes = null,
    ) {
        $allowed = [];
        foreach (ApplicationStatus::adminReviewOptions() as $option) {
            $allowed[] = $option->value;
        }

        if (! in_array($status, $allowed, true)) {
            throw new RuntimeException(
                'Please choose Under Review, Payment Verified, Accepted, Rejected, or Withdrawn.'
            );
        }

        $oldStatus = $application->status?->value;

        $application->update([
            'status' => $status,
            'admin_notes' => $notes,
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
        ]);

        $this->auditLogger->log(
            'belt_test_application.reviewed',
            $application,
            ['status' => $oldStatus],
            ['status' => $status, 'admin_notes' => $notes],
            $admin
        );

        $application = $application->fresh(['student.user', 'beltTest']);
        $this->notifications->applicationReviewed($application);

        return $application;
    }
}
