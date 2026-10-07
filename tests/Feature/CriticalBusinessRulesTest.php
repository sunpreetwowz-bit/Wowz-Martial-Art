<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TestResultOutcome;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Student;
use App\Models\User;
use App\Services\BeltProgressionService;
use App\Services\BeltTestApplicationService;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use RuntimeException;
use Tests\TestCase;

class CriticalBusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: Student, 1: BeltTest, 2: BeltTestApplication}
     */
    protected function createSubmittedApplication(float $fee = 0): array
    {
        $current = Belt::factory()->create(['rank_order' => 1]);
        $target = Belt::factory()->create(['rank_order' => 2]);
        $student = Student::factory()->create(['current_belt_id' => $current->id]);

        $test = BeltTest::factory()->create([
            'target_belt_id' => $target->id,
            'test_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00:00',
            'application_opens_at' => now()->subHour(),
            'application_closes_at' => null,
            'status' => BeltTestStatus::Open,
            'fee_amount' => $fee,
        ]);

        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Assigned,
            'assigned_at' => now(),
        ]);

        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test, PaymentMethod::Cash);

        return [$student, $test, $application];
    }

    public function test_student_cannot_update_submitted_application_or_official_belt(): void
    {
        [$student, , $application] = $this->createSubmittedApplication();

        $this->assertTrue(Gate::forUser($student->user)->denies('update', $application));
        $this->assertTrue(Gate::forUser($student->user)->denies('update', $student));
        $this->assertTrue(Gate::forUser($student->user)->denies('updateOfficialRecords', $student));

        $admin = User::factory()->admin()->create();
        $this->assertTrue(Gate::forUser($admin)->allows('update', $application));
        $this->assertTrue(Gate::forUser($admin)->allows('updateOfficialRecords', $student));
    }

    public function test_student_cannot_view_another_students_application(): void
    {
        [, , $application] = $this->createSubmittedApplication();
        $other = Student::factory()->create();

        $this->actingAs($other->user)
            ->get(route('student.applications.show', $application))
            ->assertForbidden();
    }

    public function test_student_cannot_verify_payments_or_issue_certificates(): void
    {
        [$student, , $application] = $this->createSubmittedApplication(500);
        $payment = $application->latestPayment;

        $this->actingAs($student->user)
            ->post(route('admin.payments.verify', $payment))
            ->assertForbidden();

        $admin = User::factory()->admin()->create();
        $result = app(BeltProgressionService::class)->recordResult($application, [
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => false,
        ], $admin);

        $this->actingAs($student->user)
            ->post(route('admin.certificates.issue', $result))
            ->assertForbidden();
    }

    public function test_certificate_cannot_be_issued_for_fail_result(): void
    {
        [, , $application] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();

        $result = app(BeltProgressionService::class)->recordResult($application, [
            'outcome' => TestResultOutcome::Failed->value,
        ], $admin);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Certificates can only be issued for PASS results.');

        app(CertificateService::class)->issueForResult($result, $admin);
    }

    public function test_duplicate_result_recording_is_rejected(): void
    {
        [, , $application] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();
        $service = app(BeltProgressionService::class);

        $service->recordResult($application, [
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => false,
        ], $admin);

        $this->expectException(RuntimeException::class);
        $service->recordResult($application->fresh(), [
            'outcome' => TestResultOutcome::Failed->value,
        ], $admin);
    }

    public function test_student_cannot_delete_historical_results(): void
    {
        [, , $application] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();

        $result = app(BeltProgressionService::class)->recordResult($application, [
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => false,
        ], $admin);

        $this->assertTrue(Gate::forUser($application->student->user)->denies('delete', $result));
        $this->assertDatabaseHas('belt_test_results', ['id' => $result->id, 'deleted_at' => null]);
    }

    public function test_failed_payment_callback_does_not_mark_paid(): void
    {
        $student = Student::factory()->create(['current_belt_id' => Belt::factory()->create()->id]);
        $test = BeltTest::factory()->create([
            'target_belt_id' => Belt::factory()->create()->id,
            'fee_amount' => 300,
            'application_opens_at' => now()->subHour(),
            'status' => BeltTestStatus::Open,
        ]);
        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Assigned,
            'assigned_at' => now(),
        ]);

        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test, PaymentMethod::Upi);
        $payment = $application->latestPayment;

        $this->postJson(route('payments.callback'), [
            'payment_uuid' => $payment->uuid,
            'status' => 'failed',
            'transaction_id' => 'TXN-FAIL',
        ])->assertOk();

        $this->assertSame(PaymentStatus::Failed, $payment->fresh()->status);
    }

    public function test_inactive_student_is_blocked_from_portal(): void
    {
        $student = Student::factory()->inactive()->create();

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_revoked_assignment_blocks_belt_test_access(): void
    {
        [$student, $test] = $this->createSubmittedApplication();

        // Use a fresh assigned student without application for access check
        $student = Student::factory()->create();
        $test = BeltTest::factory()->create([
            'status' => BeltTestStatus::Open,
            'application_opens_at' => now()->subHour(),
        ]);
        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Revoked,
            'assigned_at' => now(),
            'revoked_at' => now(),
        ]);

        $this->actingAs($student->user)
            ->get(route('student.belt-tests.show', $test))
            ->assertForbidden();
    }

    public function test_public_cms_and_verify_pages_load(): void
    {
        $this->get(route('about'))->assertOk();
        $this->get(route('about.team'))->assertOk();
        $this->get(route('gallery.index'))->assertOk();
        $this->get(route('events.index'))->assertOk();
        $this->get(route('achievements.index'))->assertOk();
        $this->get(route('black-belts.index'))->assertOk();
        $this->get(route('certificates.verify.form'))->assertOk();
    }

    public function test_student_cannot_access_admin_audit_or_results_create(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($student->user)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();

        $this->actingAs($student->user)
            ->get(route('admin.belt-test-results.create'))
            ->assertForbidden();
    }
}
