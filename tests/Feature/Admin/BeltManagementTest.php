<?php

namespace Tests\Feature\Admin;

use App\Enums\AccountStatus;
use App\Models\Belt;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BeltManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_belt(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.belts.store'), [
            'name' => 'White Belt',
            'color' => '#FFFFFF',
            'rank_order' => 1,
            'description' => 'Starter rank',
            'status' => AccountStatus::Active->value,
        ]);

        $response->assertRedirect(route('admin.belts.index'));
        $this->assertDatabaseHas('belts', ['name' => 'White Belt', 'rank_order' => 1]);
    }

    public function test_student_cannot_manage_belts(): void
    {
        $student = Student::factory()->create();

        $this->actingAs($student->user)
            ->get(route('admin.belts.index'))
            ->assertForbidden();
    }

    public function test_cannot_delete_belt_assigned_to_student(): void
    {
        $admin = User::factory()->admin()->create();
        $belt = Belt::factory()->create();
        Student::factory()->create(['current_belt_id' => $belt->id]);

        $this->actingAs($admin)
            ->delete(route('admin.belts.destroy', $belt))
            ->assertRedirect();

        $this->assertDatabaseHas('belts', ['id' => $belt->id]);
    }
}
