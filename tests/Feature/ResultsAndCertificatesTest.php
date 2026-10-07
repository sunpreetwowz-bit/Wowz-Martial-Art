<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Enums\CertificateStatus;
use App\Enums\PaymentMethod;
use App\Enums\TestResultOutcome;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Certificate;
use App\Models\Student;
use App\Models\User;
use App\Services\BeltTestApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultsAndCertificatesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array{0: Student, 1: \App\Models\BeltTestApplication, 2: Belt, 3: Belt}
     */
    protected function createSubmittedApplication(): array
    {
        $currentBelt = Belt::factory()->create(['rank_order' => 1, 'name' => 'White']);
        $targetBelt = Belt::factory()->create(['rank_order' => 2, 'name' => 'Yellow']);
        $student = Student::factory()->create(['current_belt_id' => $currentBelt->id]);

        $test = BeltTest::factory()->create([
            'target_belt_id' => $targetBelt->id,
            'test_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00:00',
            'application_opens_at' => now()->subHour(),
            'application_closes_at' => null,
            'status' => BeltTestStatus::Open,
            'fee_amount' => 0,
        ]);

        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Assigned,
            'assigned_at' => now(),
        ]);

        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test, PaymentMethod::Cash);

        return [$student, $application, $currentBelt, $targetBelt];
    }

    public function test_admin_pass_promotes_belt_and_issues_certificate(): void
    {
        [$student, $application, $currentBelt, $targetBelt] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.belt-test-results.store'), [
                'belt_test_application_id' => $application->id,
                'outcome' => TestResultOutcome::Passed->value,
                'issue_certificate' => '1',
                'authorized_by_name' => 'Chief Instructor',
            ])
            ->assertRedirect();

        $student->refresh();
        $application->refresh();

        $this->assertSame($targetBelt->id, $student->current_belt_id);
        $this->assertSame(ApplicationStatus::Passed, $application->status);
        $this->assertTrue($application->result->belt_updated);
        $this->assertDatabaseHas('student_belt_histories', [
            'student_id' => $student->id,
            'from_belt_id' => $currentBelt->id,
            'to_belt_id' => $targetBelt->id,
        ]);

        $certificate = Certificate::query()->where('student_id', $student->id)->first();
        $this->assertNotNull($certificate);
        $this->assertSame(CertificateStatus::Issued, $certificate->status);
        $this->assertSame('Chief Instructor', $certificate->authorized_by_name);
        $this->assertStringStartsWith('CERT-'.now()->format('Y').'-', $certificate->certificate_number);
    }

    public function test_admin_fail_does_not_change_belt_or_issue_certificate(): void
    {
        [$student, $application, $currentBelt] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post(route('admin.belt-test-results.store'), [
                'belt_test_application_id' => $application->id,
                'outcome' => TestResultOutcome::Failed->value,
            ])
            ->assertRedirect();

        $this->assertSame($currentBelt->id, $student->fresh()->current_belt_id);
        $this->assertSame(ApplicationStatus::Failed, $application->fresh()->status);
        $this->assertFalse($application->fresh()->result->belt_updated);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_student_cannot_view_another_students_certificate(): void
    {
        [$student, $application] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();
        $other = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.belt-test-results.store'), [
            'belt_test_application_id' => $application->id,
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => '1',
        ])->assertRedirect();

        $certificate = Certificate::query()->first();

        $this->actingAs($student->user)
            ->get(route('student.certificates.show', $certificate))
            ->assertOk();

        $this->actingAs($other->user)
            ->get(route('student.certificates.show', $certificate))
            ->assertForbidden();
    }

    public function test_public_verification_requires_matching_code(): void
    {
        [$student, $application] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.belt-test-results.store'), [
            'belt_test_application_id' => $application->id,
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => '1',
        ])->assertRedirect();

        $certificate = Certificate::query()->first();

        $this->get(route('certificates.verify', $certificate->certificate_number))
            ->assertOk()
            ->assertSee('Enter the verification code');

        $this->get(route('certificates.verify', [
            'certificateNumber' => $certificate->certificate_number,
            'code' => 'WRONGCODE',
        ]))
            ->assertOk()
            ->assertSee('No matching certificate found');

        $this->get(route('certificates.verify', [
            'certificateNumber' => $certificate->certificate_number,
            'code' => $certificate->verification_code,
        ]))
            ->assertOk()
            ->assertSee('Certificate verified')
            ->assertSee($certificate->student_name_snapshot);
    }

    public function test_correcting_pass_to_fail_reverts_belt_and_revokes_certificate(): void
    {
        [$student, $application, $currentBelt, $targetBelt] = $this->createSubmittedApplication();
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.belt-test-results.store'), [
            'belt_test_application_id' => $application->id,
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => '1',
        ])->assertRedirect();

        $result = $application->fresh()->result;
        $certificate = $result->certificate;

        $this->actingAs($admin)->put(route('admin.belt-test-results.update', $result), [
            'outcome' => TestResultOutcome::Failed->value,
            'admin_notes' => 'Score recount',
        ])->assertRedirect();

        $this->assertSame($currentBelt->id, $student->fresh()->current_belt_id);
        $this->assertSame(ApplicationStatus::Failed, $application->fresh()->status);
        $this->assertFalse($result->fresh()->belt_updated);
        $this->assertSame(CertificateStatus::Revoked, $certificate->fresh()->status);
        $this->assertNotSame($targetBelt->id, $student->fresh()->current_belt_id);
    }

    public function test_student_cannot_record_results(): void
    {
        [, $application] = $this->createSubmittedApplication();
        $studentUser = Student::factory()->create()->user;

        $this->actingAs($studentUser)
            ->post(route('admin.belt-test-results.store'), [
                'belt_test_application_id' => $application->id,
                'outcome' => TestResultOutcome::Passed->value,
            ])
            ->assertForbidden();
    }
}
