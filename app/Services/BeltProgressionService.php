<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\BeltHistorySource;
use App\Enums\TestResultOutcome;
use App\Models\BeltTestApplication;
use App\Models\BeltTestResult;
use App\Models\Student;
use App\Models\StudentBeltHistory;
use App\Models\User;
use App\Support\AdminDashboardStats;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class BeltProgressionService
{
    protected $auditLogger;
    protected $certificateService;
    protected $notifications;

    public function __construct(
        AuditLogger $auditLogger,
        CertificateService $certificateService,
        NotificationService $notifications,
    )
    {
        $this->auditLogger = $auditLogger;
        $this->certificateService = $certificateService;
        $this->notifications = $notifications;
    }

    /**
     * @param  array{outcome: string, score?: mixed, admin_notes?: string|null, result_date?: string|null, issue_certificate?: bool}  $data
     */
    public function recordResult(BeltTestApplication $application, array $data, User $admin) {
        $outcome = TestResultOutcome::from($data['outcome']);

        return DB::transaction(function () use ($application, $data, $admin, $outcome) {
            $application = BeltTestApplication::query()
                ->whereKey($application->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($application->result()->exists()) {
                throw new RuntimeException('A result already exists for this application. Use correction instead.');
            }

            $application->loadMissing(['student', 'beltTest.targetBelt', 'targetBelt']);

            $result = BeltTestResult::query()->create([
                'belt_test_application_id' => $application->id,
                'belt_test_id' => $application->belt_test_id,
                'student_id' => $application->student_id,
                'outcome' => $outcome,
                'score' => $data['score'] ?? null,
                'admin_notes' => $data['admin_notes'] ?? null,
                'result_date' => $data['result_date'] ?? now()->toDateString(),
                'belt_updated' => false,
                'recorded_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);

            $application->update([
                'status' => $outcome === TestResultOutcome::Passed
                    ? ApplicationStatus::Passed
                    : ApplicationStatus::Failed,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            if ($outcome === TestResultOutcome::Passed) {
                $this->promoteStudent($application->student, $application, $result, $admin);

                if ($data['issue_certificate'] ?? true) {
                    $this->certificateService->issueForResult(
                        $result,
                        $admin,
                        $data['authorized_by_name'] ?? null,
                    );
                }
            }

            $this->auditLogger->log(
                'belt_test_result.recorded',
                $result,
                null,
                [
                    'outcome' => $outcome->value,
                    'student_id' => $application->student_id,
                    'belt_test_id' => $application->belt_test_id,
                ],
                $admin
            );

            AdminDashboardStats::forget();

            $result = $result->fresh(['student.user', 'application', 'certificate', 'beltTest']);
            $this->notifications->resultRecorded($result);

            return $result;
        });
    }

    /**
     * Correct a previously recorded result with full audit trail.
     *
     * @param  array{outcome: string, score?: mixed, admin_notes?: string|null, result_date?: string|null, issue_certificate?: bool}  $data
     */
    public function correctResult(BeltTestResult $result, array $data, User $admin) {
        $newOutcome = TestResultOutcome::from($data['outcome']);
        $oldOutcome = $result->outcome;

        return DB::transaction(function () use ($result, $data, $admin, $newOutcome, $oldOutcome) {
            $result->loadMissing(['student', 'application.beltTest.targetBelt', 'application.targetBelt', 'certificate']);

            $oldValues = [
                'outcome' => $oldOutcome?->value,
                'belt_updated' => $result->belt_updated,
            ];

            $result->update([
                'outcome' => $newOutcome,
                'score' => $data['score'] ?? $result->score,
                'admin_notes' => $data['admin_notes'] ?? $result->admin_notes,
                'result_date' => $data['result_date'] ?? $result->result_date,
                'updated_by' => $admin->id,
            ]);

            $result->application->update([
                'status' => $newOutcome === TestResultOutcome::Passed
                    ? ApplicationStatus::Passed
                    : ApplicationStatus::Failed,
                'reviewed_by' => $admin->id,
                'reviewed_at' => now(),
            ]);

            // PASS -> FAIL: revert belt if previously updated
            if ($oldOutcome === TestResultOutcome::Passed && $newOutcome === TestResultOutcome::Failed && $result->belt_updated) {
                $this->revertPromotion($result, $admin);
                if ($result->certificate && $result->certificate->isIssued()) {
                    $this->certificateService->revoke(
                        $result->certificate,
                        $admin,
                        'Result corrected from PASS to FAIL.'
                    );
                }
            }

            // FAIL -> PASS: promote and optionally issue certificate
            if ($oldOutcome === TestResultOutcome::Failed && $newOutcome === TestResultOutcome::Passed) {
                $this->promoteStudent($result->student, $result->application, $result, $admin);
                if ($data['issue_certificate'] ?? true) {
                    $this->certificateService->issueForResult($result->fresh(), $admin);
                }
            }

            $this->auditLogger->log(
                'belt_test_result.corrected',
                $result,
                $oldValues,
                [
                    'outcome' => $newOutcome->value,
                    'belt_updated' => $result->fresh()->belt_updated,
                ],
                $admin
            );

            return $result->fresh(['student', 'application', 'certificate', 'beltTest']);
        });
    }

    protected function promoteStudent(
        Student $student,
        BeltTestApplication $application,
        BeltTestResult $result,
        User $admin,
    ) {
        $targetBeltId = $application->target_belt_id;
        if (! $targetBeltId) {
            throw new InvalidArgumentException('Application is missing target belt.');
        }

        $fromBeltId = $student->current_belt_id;

        if ((int) $fromBeltId === (int) $targetBeltId) {
            $result->update(['belt_updated' => true]);

            return;
        }

        $student->update(['current_belt_id' => $targetBeltId]);

        StudentBeltHistory::query()->create([
            'student_id' => $student->id,
            'from_belt_id' => $fromBeltId,
            'to_belt_id' => $targetBeltId,
            'source' => BeltHistorySource::BeltTest,
            'source_ref_type' => BeltTestResult::class,
            'source_ref_id' => $result->id,
            'notes' => 'Promoted after passing belt test.',
            'created_by' => $admin->id,
        ]);

        $result->update(['belt_updated' => true]);

        $this->auditLogger->log(
            'student.belt_changed',
            $student,
            ['current_belt_id' => $fromBeltId],
            ['current_belt_id' => $targetBeltId, 'source' => 'belt_test'],
            $admin
        );
    }

    protected function revertPromotion(BeltTestResult $result, User $admin)
    {
        $student = $result->student;
        $application = $result->application;
        $currentBeltId = $student->current_belt_id;
        $previousBeltId = $application->current_belt_id;

        if ((int) $currentBeltId === (int) $application->target_belt_id && $previousBeltId) {
            $student->update(['current_belt_id' => $previousBeltId]);

            StudentBeltHistory::query()->create([
                'student_id' => $student->id,
                'from_belt_id' => $currentBeltId,
                'to_belt_id' => $previousBeltId,
                'source' => BeltHistorySource::AdminCorrection,
                'source_ref_type' => BeltTestResult::class,
                'source_ref_id' => $result->id,
                'notes' => 'Belt reverted after result correction (PASS → FAIL).',
                'created_by' => $admin->id,
            ]);
        }

        $result->update(['belt_updated' => false]);
    }
}
