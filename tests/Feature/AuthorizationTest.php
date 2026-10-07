<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($student->user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_cannot_access_student_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('student.dashboard'))
            ->assertForbidden();
    }

    public function test_student_can_access_student_dashboard(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk();
    }

    public function test_student_cannot_view_another_students_model_via_policy(): void
    {
        $studentA = Student::factory()->create();
        $studentB = Student::factory()->create();

        $this->assertFalse($studentA->user->can('view', $studentB));
        $this->assertTrue($studentA->user->can('view', $studentA));
        $this->assertTrue(User::factory()->admin()->create()->can('view', $studentB));
    }
}
