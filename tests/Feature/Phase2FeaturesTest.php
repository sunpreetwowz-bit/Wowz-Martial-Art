<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\ApplicationStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Enums\CertificateStatus;
use App\Enums\CompetitionResponseStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TestResultOutcome;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Certificate;
use App\Models\CompetitionForm;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AcademyAlert;
use App\Services\BeltTestApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class Phase2FeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_issued_certificate_can_be_downloaded_as_pdf(): void
    {
        Storage::fake('local');

        $currentBelt = Belt::factory()->create(['rank_order' => 1, 'name' => 'White']);
        $targetBelt = Belt::factory()->create(['rank_order' => 2, 'name' => 'Yellow']);
        $student = Student::factory()->create(['current_belt_id' => $currentBelt->id]);
        $admin = User::factory()->admin()->create();

        $test = BeltTest::factory()->create([
            'target_belt_id' => $targetBelt->id,
            'test_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00:00',
            'application_opens_at' => now()->subHour(),
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

        $this->actingAs($admin)
            ->post(route('admin.belt-test-results.store'), [
                'belt_test_application_id' => $application->id,
                'outcome' => TestResultOutcome::Passed->value,
                'issue_certificate' => '1',
                'authorized_by_name' => 'Chief Instructor',
            ])
            ->assertRedirect();

        $certificate = Certificate::query()->firstOrFail();
        $this->assertSame(CertificateStatus::Issued, $certificate->status);

        $this->actingAs($student->user)
            ->get(route('student.certificates.download', $certificate))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($admin)
            ->get(route('admin.certificates.download', $certificate))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_student_can_open_google_form_flow_and_mark_responded(): void
    {
        Storage::fake('local');
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'State Championship Entry',
            'description' => 'Fill Google Form after downloading PDF.',
            'google_form_url' => 'https://docs.google.com/forms/d/e/example/viewform',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('entry.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $form = CompetitionForm::query()->firstOrFail();
        $this->assertSame('https://docs.google.com/forms/d/e/example/viewform', $form->google_form_url);

        $this->actingAs($student->user)
            ->get(route('student.competition-forms.show', $form))
            ->assertOk()
            ->assertSee('Open Google Form')
            ->assertSee("I've submitted the Google Form", false);

        $this->actingAs($student->user)
            ->post(route('student.competition-forms.responded', $form))
            ->assertRedirect(route('student.competition-forms.show', $form));

        $this->assertDatabaseHas('competition_form_assignments', [
            'competition_form_id' => $form->id,
            'student_id' => $student->id,
            'response_status' => CompetitionResponseStatus::Responded->value,
        ]);

        Notification::assertSentTo($admin, AcademyAlert::class, function (AcademyAlert $notification) {
            return ($notification->meta['event'] ?? null) === 'competition_responded';
        });
    }

    public function test_sandbox_checkout_can_mark_online_payment_paid(): void
    {
        $currentBelt = Belt::factory()->create(['rank_order' => 1]);
        $targetBelt = Belt::factory()->create(['rank_order' => 2]);
        $student = Student::factory()->create(['current_belt_id' => $currentBelt->id]);

        $test = BeltTest::factory()->create([
            'target_belt_id' => $targetBelt->id,
            'test_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00:00',
            'application_opens_at' => now()->subHour(),
            'status' => BeltTestStatus::Open,
            'fee_amount' => 750,
        ]);

        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Assigned,
            'assigned_at' => now(),
        ]);

        $this->actingAs($student->user)
            ->post(route('student.belt-tests.apply.submit', $test), [
                'payment_method' => PaymentMethod::Upi->value,
                'acknowledge' => '1',
            ])
            ->assertRedirect();

        $application = $student->applications()->latest('id')->firstOrFail();
        $payment = $application->latestPayment;

        $this->assertSame(PaymentStatus::Pending, $payment->status);
        $this->assertSame(ApplicationStatus::PaymentPending, $application->status);

        $this->actingAs($student->user)
            ->get(route('student.payments.checkout', $payment))
            ->assertOk()
            ->assertSee('Sandbox checkout')
            ->assertSee('Simulate successful payment');

        $this->actingAs($student->user)
            ->post(route('student.payments.checkout.complete', $payment), [
                'outcome' => 'success',
            ])
            ->assertRedirect(route('student.applications.show', $application));

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(ApplicationStatus::PaymentVerified, $application->fresh()->status);
    }

    public function test_mail_channel_enabled_when_configured(): void
    {
        config(['academy.notifications.mail' => true]);

        $student = Student::factory()->create();
        $notification = new AcademyAlert('Hello', 'World', '/student/dashboard');

        $this->assertContains('mail', $notification->via($student->user));
        $this->assertContains('database', $notification->via($student->user));
    }
}
