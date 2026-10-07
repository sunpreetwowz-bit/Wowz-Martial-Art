<?php

namespace Tests\Unit;

use App\Enums\TestResultOutcome;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\Student;
use App\Models\User;
use App\Models\Certificate;
use App\Services\BeltProgressionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_certificate_numbers_increment_uniquely(): void
    {
        $admin = User::factory()->admin()->create();
        $current = Belt::factory()->create(['rank_order' => 1]);
        $target = Belt::factory()->create(['rank_order' => 2, 'name' => 'Yellow']);
        $studentA = Student::factory()->create(['current_belt_id' => $current->id]);
        $studentB = Student::factory()->create(['current_belt_id' => $current->id]);

        $test = BeltTest::factory()->create(['target_belt_id' => $target->id]);

        foreach ([$studentA, $studentB] as $student) {
            $application = BeltTestApplication::query()->create([
                'belt_test_id' => $test->id,
                'student_id' => $student->id,
                'current_belt_id' => $current->id,
                'target_belt_id' => $target->id,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            app(BeltProgressionService::class)->recordResult($application, [
                'outcome' => TestResultOutcome::Passed->value,
                'issue_certificate' => true,
            ], $admin);
        }

        $certs = Certificate::query()->orderBy('id')->pluck('certificate_number');

        $this->assertCount(2, $certs);
        $this->assertNotSame($certs[0], $certs[1]);
        $this->assertMatchesRegularExpression('/^CERT-\d{4}-\d{6}$/', $certs[0]);
        $this->assertMatchesRegularExpression('/^CERT-\d{4}-\d{6}$/', $certs[1]);
    }
}
