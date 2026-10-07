<?php

namespace Tests\Feature;

use App\Enums\ApplicationStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Student;
use App\Models\User;
use App\Services\BeltTestAccessService;
use App\Services\BeltTestApplicationService;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeltTestSystemTest extends TestCase
{
    use RefreshDatabase;

    protected function createAssignedPair(): array
    {
        $belt = Belt::factory()->create(['rank_order' => 1]);
        $student = Student::factory()->create(['current_belt_id' => $belt->id]);
        $target = Belt::factory()->create(['rank_order' => 2]);

        $test = BeltTest::factory()->create([
            'target_belt_id' => $target->id,
            'test_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00:00',
            'application_opens_at' => now()->subHour(),
            'application_closes_at' => null,
            'status' => BeltTestStatus::Open,
            'fee_amount' => 500,
        ]);

        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Assigned,
            'assigned_at' => now(),
        ]);

        return [$student, $test];
    }

    public function test_assigned_student_can_access_and_unassigned_cannot(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $other = Student::factory()->create();

        $this->actingAs($student->user)
            ->get(route('student.belt-tests.show', $test))
            ->assertOk();

        $this->actingAs($other->user)
            ->get(route('student.belt-tests.show', $test))
            ->assertForbidden();
    }

    public function test_application_closes_at_exact_deadline_and_rejects_after(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $access = app(BeltTestAccessService::class);

        $closesAt = $test->effectiveApplicationClosesAt();

        Carbon::setTestNow($closesAt->copy()->subSecond());
        $this->assertTrue($access->canApply($student->fresh(), $test->fresh()));

        Carbon::setTestNow($closesAt->copy());
        $this->assertFalse($access->canApply($student->fresh(), $test->fresh()));

        Carbon::setTestNow($closesAt->copy()->addMinute());
        $this->assertFalse($access->canApply($student->fresh(), $test->fresh()));

        Carbon::setTestNow();
    }

    public function test_student_cannot_submit_twice_and_cash_stays_pending(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $service = app(BeltTestApplicationService::class);

        $application = $service->submit($student, $test, PaymentMethod::Cash, ['acknowledge' => true]);

        $this->assertSame(ApplicationStatus::PaymentPending, $application->status);
        $this->assertSame(PaymentStatus::Pending, $application->latestPayment->status);

        $this->expectException(\App\Exceptions\BeltTestAccessException::class);
        $service->submit($student, $test, PaymentMethod::Cash, ['acknowledge' => true]);
    }

    public function test_admin_can_verify_offline_payment(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $admin = User::factory()->admin()->create();
        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test, PaymentMethod::Cash);

        $payment = $application->latestPayment;

        $this->actingAs($admin)
            ->post(route('admin.payments.verify', $payment), ['notes' => 'Cash received'])
            ->assertRedirect();

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(ApplicationStatus::PaymentVerified, $application->fresh()->status);
    }

    public function test_http_submit_rejected_when_closed(): void
    {
        [$student, $test] = $this->createAssignedPair();
        Carbon::setTestNow($test->effectiveApplicationClosesAt());

        $this->actingAs($student->user)
            ->post(route('student.belt-tests.apply.submit', $test), [
                'payment_method' => PaymentMethod::Cash->value,
                'acknowledge' => '1',
            ])
            ->assertRedirect()
            ->assertSessionHas('error');

        Carbon::setTestNow();
    }

    public function test_admin_can_review_application_with_simple_status(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $test->update(['fee_amount' => 0]);
        $admin = User::factory()->admin()->create();

        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test->fresh(), PaymentMethod::Cash);

        $this->actingAs($admin)
            ->post(route('admin.belt-test-applications.review', $application), [
                'status' => ApplicationStatus::Accepted->value,
                'admin_notes' => 'Looks good',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertSame(ApplicationStatus::Accepted, $application->fresh()->status);
    }

    public function test_payment_callback_is_idempotent(): void
    {
        [$student, $test] = $this->createAssignedPair();
        $application = app(BeltTestApplicationService::class)
            ->submit($student, $test, PaymentMethod::Upi);
        $payment = $application->latestPayment;

        $payload = [
            'payment_uuid' => $payment->uuid,
            'status' => 'success',
            'transaction_id' => 'TXN-1',
        ];

        $this->postJson(route('payments.callback'), $payload)->assertOk();
        $this->postJson(route('payments.callback'), $payload)->assertOk();

        $this->assertSame(1, $payment->fresh()->where('status', PaymentStatus::Paid)->count() ? 1 : 0);
        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame('TXN-1', $payment->fresh()->gateway_transaction_id);
    }
}
