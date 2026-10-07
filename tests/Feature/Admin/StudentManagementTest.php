<?php

namespace Tests\Feature\Admin;

use App\Enums\AccountStatus;
use App\Models\Belt;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_student_with_auto_password_and_belt_history(): void
    {
        $admin = User::factory()->admin()->create();
        $belt = Belt::factory()->create(['rank_order' => 1]);

        $response = $this->actingAs($admin)->post(route('admin.students.store'), [
            'name' => 'Ram Kumar',
            'email' => 'ram@example.com',
            'phone' => '9876543210',
            'current_belt_id' => $belt->id,
            'status' => AccountStatus::Active->value,
            'joining_date' => now()->toDateString(),
        ]);

        $student = Student::query()->where('student_code', 'like', 'WMA-%')->first();

        $this->assertNotNull($student);
        $response->assertRedirect(route('admin.students.show', $student));
        $response->assertSessionHas('temporary_password');

        $this->assertDatabaseHas('users', [
            'email' => 'ram@example.com',
            'role' => 'student',
        ]);

        $this->assertDatabaseHas('student_belt_histories', [
            'student_id' => $student->id,
            'to_belt_id' => $belt->id,
            'source' => 'initial',
        ]);
    }

    public function test_student_cannot_access_admin_student_list(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($student->user)
            ->get(route('admin.students.index'))
            ->assertForbidden();
    }

    public function test_student_can_view_own_profile_and_dashboard(): void
    {
        $belt = Belt::factory()->create();
        $student = Student::factory()->create(['current_belt_id' => $belt->id]);

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee($student->student_code)
            ->assertSee($belt->name);

        $this->actingAs($student->user)
            ->get(route('student.profile'))
            ->assertOk()
            ->assertSee($student->student_code);
    }

    public function test_admin_can_deactivate_student(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.students.deactivate', $student))
            ->assertRedirect();

        $this->assertSame(AccountStatus::Inactive, $student->fresh()->status);
        $this->assertSame(AccountStatus::Inactive, $student->user->fresh()->status);
    }
}
