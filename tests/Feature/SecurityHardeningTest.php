<?php

namespace Tests\Feature;

use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\TestResultOutcome;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\BeltTestResult;
use App\Models\Student;
use App\Models\User;
use App\Services\BeltTestApplicationService;
use App\Support\SafeRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_safe_redirect_rejects_external_urls(): void
    {
        $this->assertSame('/student/dashboard', SafeRedirect::internal('/student/dashboard'));
        $this->assertSame('/', SafeRedirect::internal('https://evil.test/phish'));
        $this->assertSame('/', SafeRedirect::internal('//evil.test'));
        $this->assertSame('/fallback', SafeRedirect::internal(null, '/fallback'));
    }

    public function test_payment_callback_requires_signature_when_configured(): void
    {
        config(['academy.payments.callback_secret' => 'test-secret']);

        $student = Student::factory()->create(['current_belt_id' => Belt::factory()->create()->id]);
        $test = BeltTest::factory()->create([
            'fee_amount' => 200,
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
            'status' => 'success',
            'transaction_id' => 'TXN-1',
            'signature' => 'bad',
        ])->assertForbidden();

        $signature = hash_hmac('sha256', $payment->uuid.'|success|TXN-1', 'test-secret');

        $this->postJson(route('payments.callback'), [
            'payment_uuid' => $payment->uuid,
            'status' => 'success',
            'transaction_id' => 'TXN-1',
            'signature' => $signature,
        ])->assertOk();

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
    }

    public function test_result_search_filter_does_not_bypass_outcome(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create(['current_belt_id' => Belt::factory()->create()->id]);
        $test = BeltTest::factory()->create(['title' => 'Alpha Grading']);
        $otherTest = BeltTest::factory()->create(['title' => 'Beta Grading']);

        $pass = BeltTestResult::query()->create([
            'belt_test_application_id' => \App\Models\BeltTestApplication::query()->create([
                'belt_test_id' => $test->id,
                'student_id' => $student->id,
                'current_belt_id' => $student->current_belt_id,
                'target_belt_id' => $test->target_belt_id,
                'status' => 'passed',
                'submitted_at' => now(),
            ])->id,
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'outcome' => TestResultOutcome::Passed,
            'result_date' => now()->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        BeltTestResult::query()->create([
            'belt_test_application_id' => \App\Models\BeltTestApplication::query()->create([
                'belt_test_id' => $otherTest->id,
                'student_id' => $student->id,
                'current_belt_id' => $student->current_belt_id,
                'target_belt_id' => $otherTest->target_belt_id,
                'status' => 'failed',
                'submitted_at' => now(),
            ])->id,
            'belt_test_id' => $otherTest->id,
            'student_id' => $student->id,
            'outcome' => TestResultOutcome::Failed,
            'result_date' => now()->toDateString(),
            'recorded_by' => $admin->id,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.belt-test-results.index', [
                'outcome' => 'failed',
                'q' => 'Alpha',
            ]))
            ->assertOk()
            ->assertDontSee('Alpha Grading')
            ->assertDontSee($pass->beltTest->title);
    }

    public function test_security_headers_are_present(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
