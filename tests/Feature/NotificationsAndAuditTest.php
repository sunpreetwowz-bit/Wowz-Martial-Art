<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Enums\PaymentMethod;
use App\Enums\TestResultOutcome;
use App\Models\AuditLog;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Student;
use App\Models\User;
use App\Notifications\AcademyAlert;
use App\Services\BeltTestApplicationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NotificationsAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function createAssignedPair(): array
    {
        $belt = Belt::factory()->create(['rank_order' => 1]);
        $target = Belt::factory()->create(['rank_order' => 2]);
        $student = Student::factory()->create(['current_belt_id' => $belt->id]);

        $test = BeltTest::factory()->create([
            'target_belt_id' => $target->id,
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

        return [$student, $test, $target];
    }

    public function test_application_submit_notifies_student_and_creates_audit_log(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $admin = User::factory()->admin()->create();

        app(BeltTestApplicationService::class)->submit($student, $test, PaymentMethod::Cash);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'belt_test_application.submitted',
        ]);

        $this->assertTrue(
            $student->user->notifications()->where('type', AcademyAlert::class)->exists()
        );
        $this->assertTrue(
            $admin->fresh()->notifications()->where('type', AcademyAlert::class)->exists()
        );
    }

    public function test_payment_verify_notifies_student(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $test->update(['fee_amount' => 500]);
        $admin = User::factory()->admin()->create();

        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test->fresh(), PaymentMethod::Cash);

        $student->user->notifications()->delete();

        $this->actingAs($admin)
            ->post(route('admin.payments.verify', $application->latestPayment), ['notes' => 'OK'])
            ->assertRedirect();

        $this->assertTrue(
            $student->user->fresh()->notifications()
                ->get()
                ->contains(fn ($n) => ($n->data['meta']['event'] ?? null) === 'payment_verified')
        );
    }

    public function test_result_and_certificate_notify_student(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $admin = User::factory()->admin()->create();
        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test, PaymentMethod::Cash);

        $student->user->notifications()->delete();

        $this->actingAs($admin)->post(route('admin.belt-test-results.store'), [
            'belt_test_application_id' => $application->id,
            'outcome' => TestResultOutcome::Passed->value,
            'issue_certificate' => '1',
        ])->assertRedirect();

        $events = $student->user->fresh()->notifications->pluck('data.meta.event');
        $this->assertTrue($events->contains('result_recorded'));
        $this->assertTrue($events->contains('certificate_issued'));
    }

    public function test_competition_assignment_notifies_student(): void
    {
        Storage::fake('local');
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'Notify Meet',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('notify.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $this->assertTrue(
            $student->user->fresh()->notifications()
                ->get()
                ->contains(fn ($n) => ($n->data['meta']['event'] ?? null) === 'competition_assigned')
        );
    }

    public function test_student_can_view_and_mark_notifications_read(): void
    {
        [$student, $test] = $this->createAssignedPair();
        app(BeltTestApplicationService::class)->submit($student, $test, PaymentMethod::Cash);

        $notification = $student->user->notifications()->first();

        $this->actingAs($student->user)
            ->get(route('student.notifications.index'))
            ->assertOk()
            ->assertSee('Application received');

        $this->actingAs($student->user)
            ->post(route('student.notifications.read', $notification->id))
            ->assertRedirect();

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_admin_can_view_audit_logs_and_student_cannot(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        AuditLog::query()->create([
            'user_id' => $admin->id,
            'action' => 'test.action',
            'subject_type' => Student::class,
            'subject_id' => $student->id,
            'new_values' => ['ok' => true],
        ]);

        $this->actingAs($admin)
            ->get(route('admin.audit-logs.index'))
            ->assertOk()
            ->assertSee('test.action');

        $this->actingAs($student->user)
            ->get(route('admin.audit-logs.index'))
            ->assertForbidden();
    }

    public function test_deadline_command_notifies_once(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $test->update([
            'application_closes_at' => now()->addHours(6),
            'status' => BeltTestStatus::Open,
        ]);

        $this->artisan('academy:notify-deadlines')->assertSuccessful();
        $this->artisan('academy:notify-deadlines')->assertSuccessful();

        $deadlineNotes = $student->user->fresh()->notifications()
            ->get()
            ->filter(fn ($n) => ($n->data['meta']['event'] ?? null) === 'belt_test_deadline');

        $this->assertCount(1, $deadlineNotes);
    }
}
