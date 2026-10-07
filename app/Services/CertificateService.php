<?php

namespace App\Services;

use App\Enums\CertificateStatus;
use App\Enums\TestResultOutcome;
use App\Models\BeltTestResult;
use App\Models\Certificate;
use App\Models\User;
use App\Support\AdminDashboardStats;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Issue, revoke, verify, and download student certificates.
 */
class CertificateService
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

    public function issueForResult(BeltTestResult $result, User $admin, ?string $authorizedByName = null) {
        $result->loadMissing(['student.user', 'application.targetBelt', 'beltTest', 'certificate']);

        if ($result->outcome !== TestResultOutcome::Passed) {
            throw new RuntimeException('Certificates can only be issued for PASS results.');
        }

        // Already issued — return it
        if ($result->certificate && $result->certificate->status === CertificateStatus::Issued) {
            return $result->certificate;
        }

        return DB::transaction(function () use ($result, $admin, $authorizedByName) {
            $belt = $result->application->targetBelt;
            $student = $result->student;
            $common = [
                'verification_code' => Str::upper(Str::random(16)),
                'student_name_snapshot' => $student->user->name,
                'student_code_snapshot' => $student->student_code,
                'belt_name_snapshot' => $belt->name,
                'test_date' => $result->beltTest?->test_date,
                'issued_on' => now()->toDateString(),
                'authorized_by_name' => $authorizedByName ?: $admin->name,
                'status' => CertificateStatus::Issued,
                'revoked_at' => null,
                'revoke_reason' => null,
                'issued_by' => $admin->id,
            ];

            if ($result->certificate) {
                // Re-issue a previously revoked certificate
                $certificate = $result->certificate;
                $certificate->update(array_merge($common, [
                    'certificate_number' => $certificate->status === CertificateStatus::Revoked
                        ? $this->generateCertificateNumber()
                        : $certificate->certificate_number,
                ]));
            } else {
                $certificate = Certificate::query()->create(array_merge($common, [
                    'certificate_number' => $this->generateCertificateNumber(),
                    'student_id' => $student->id,
                    'belt_test_id' => $result->belt_test_id,
                    'belt_test_result_id' => $result->id,
                    'belt_id' => $belt->id,
                ]));
            }

            $this->auditLogger->log(
                'certificate.issued',
                $certificate,
                null,
                [
                    'certificate_number' => $certificate->certificate_number,
                    'student_id' => $student->id,
                    'belt_id' => $belt->id,
                ],
                $admin
            );

            AdminDashboardStats::forget();

            $certificate = $certificate->fresh(['student.user']);
            $this->notifications->certificateIssued($certificate);

            return $certificate;
        });
    }

    public function revoke(Certificate $certificate, User $admin, string $reason) {
        if ($certificate->status === CertificateStatus::Revoked) {
            return $certificate;
        }

        $certificate->update([
            'status' => CertificateStatus::Revoked,
            'revoked_at' => now(),
            'revoke_reason' => $reason,
        ]);

        $this->auditLogger->log(
            'certificate.revoked',
            $certificate,
            ['status' => 'issued'],
            ['status' => 'revoked', 'reason' => $reason],
            $admin
        );

        $certificate = $certificate->fresh(['student.user']);
        $this->notifications->certificateRevoked($certificate);

        return $certificate;
    }

    public function findForPublicVerification(string $certificateNumber, ?string $verificationCode = null) {
        $query = Certificate::query()->where('certificate_number', $certificateNumber);

        if ($verificationCode) {
            $query->where('verification_code', $verificationCode);
        }

        return $query->first();
    }

    public function downloadPdf(Certificate $certificate) {
        if (! $certificate->isIssued()) {
            throw new RuntimeException('Only issued certificates can be downloaded as PDF.');
        }

        $pdf = Pdf::loadView('certificates.pdf', [
            'certificate' => $certificate,
        ])->setPaper('a4', 'landscape');

        $relativePath = 'certificates/'.$certificate->certificate_number.'.pdf';
        Storage::disk('local')->put($relativePath, $pdf->output());
        $certificate->update(['file_path' => $relativePath]);

        return $pdf->download($certificate->certificate_number.'.pdf');
    }

    /** Build next number like CERT-2026-000001 */
    protected function generateCertificateNumber()
    {
        $prefix = config('academy.certificate_prefix', 'CERT');
        $year = now(config('app.timezone'))->format('Y');

        return DB::transaction(function () use ($prefix, $year) {
            $latest = Certificate::withTrashed()
                ->where('certificate_number', 'like', "{$prefix}-{$year}-%")
                ->orderByDesc('certificate_number')
                ->lockForUpdate()
                ->value('certificate_number');

            $next = 1;
            if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
                $next = ((int) $matches[1]) + 1;
            }

            return sprintf('%s-%s-%06d', $prefix, $year, $next);
        });
    }
}
