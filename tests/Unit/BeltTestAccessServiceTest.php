<?php

namespace Tests\Unit;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Exceptions\BeltTestAccessException;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Student;
use App\Services\BeltTestAccessService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeltTestAccessServiceTest extends TestCase
{
    use RefreshDatabase;

    protected BeltTestAccessService $access;

    protected function setUp(): void
    {
        parent::setUp();
        $this->access = app(BeltTestAccessService::class);
    }

    /**
     * @return array{0: Student, 1: BeltTest}
     */
    protected function makePair(array $testOverrides = []): array
    {
        $belt = Belt::factory()->create();
        $student = Student::factory()->create(['current_belt_id' => $belt->id]);
        $test = BeltTest::factory()->create(array_merge([
            'target_belt_id' => Belt::factory()->create()->id,
            'test_date' => now()->addDay()->toDateString(),
            'start_time' => '17:00:00',
            'application_opens_at' => now()->subHour(),
            'application_closes_at' => null,
            'status' => BeltTestStatus::Open,
        ], $testOverrides));

        BeltTestAssignment::query()->create([
            'belt_test_id' => $test->id,
            'student_id' => $student->id,
            'status' => AssignmentStatus::Assigned,
            'assigned_at' => now(),
        ]);

        return [$student, $test->fresh()];
    }

    public function test_default_close_is_thirty_minutes_before_start(): void
    {
        [, $test] = $this->makePair([
            'test_date' => '2026-10-10',
            'start_time' => '17:00:00',
            'application_closes_at' => null,
        ]);

        $closes = $test->effectiveApplicationClosesAt();

        $this->assertSame('2026-10-10 16:30:00', $closes->format('Y-m-d H:i:s'));
    }

    public function test_inactive_student_cannot_apply(): void
    {
        [$student, $test] = $this->makePair();
        $student->update(['status' => AccountStatus::Inactive]);

        $this->expectException(BeltTestAccessException::class);
        $this->access->assertCanApply($student->fresh(), $test);
    }

    public function test_unassigned_and_revoked_students_cannot_apply(): void
    {
        [$student, $test] = $this->makePair();
        $other = Student::factory()->create();

        $this->assertFalse($this->access->canApply($other, $test));

        BeltTestAssignment::query()
            ->where('belt_test_id', $test->id)
            ->where('student_id', $student->id)
            ->update(['status' => AssignmentStatus::Revoked]);

        $this->assertFalse($this->access->canApply($student->fresh(), $test->fresh()));
    }

    public function test_cancelled_test_rejects_applications(): void
    {
        [$student, $test] = $this->makePair(['status' => BeltTestStatus::Cancelled]);

        $this->assertFalse($this->access->canApply($student, $test));
    }

    public function test_application_not_open_yet(): void
    {
        [$student, $test] = $this->makePair([
            'application_opens_at' => now()->addHour(),
        ]);

        $this->assertFalse($this->access->canApply($student, $test));
    }

    public function test_window_is_half_open_at_exact_close(): void
    {
        [$student, $test] = $this->makePair([
            'application_closes_at' => Carbon::parse('2026-10-10 12:00:00', config('app.timezone')),
        ]);

        Carbon::setTestNow(Carbon::parse('2026-10-10 11:59:59', config('app.timezone')));
        $this->assertTrue($this->access->canApply($student->fresh(), $test->fresh()));

        Carbon::setTestNow(Carbon::parse('2026-10-10 12:00:00', config('app.timezone')));
        $this->assertFalse($this->access->canApply($student->fresh(), $test->fresh()));

        Carbon::setTestNow();
    }
}
