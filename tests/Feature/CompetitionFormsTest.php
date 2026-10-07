<?php

namespace Tests\Feature;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Enums\CompetitionResponseStatus;
use App\Models\CompetitionForm;
use App\Models\CompetitionFormAssignment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompetitionFormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    public function test_admin_can_create_form_and_assign_students(): void
    {
        $admin = User::factory()->admin()->create();
        $assigned = Student::factory()->create();
        $other = Student::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'Punjab State Taekwondo Championship',
            'description' => 'Bring completed form to the academy.',
            'deadline_at' => now()->addDays(7)->format('Y-m-d\TH:i'),
            'status' => AccountStatus::Active->value,
            'student_ids' => [$assigned->id],
            'pdf' => UploadedFile::fake()->create('championship.pdf', 200, 'application/pdf'),
        ]);

        $form = CompetitionForm::query()->first();
        $this->assertNotNull($form);
        $response->assertRedirect(route('admin.competition-forms.show', $form));

        $this->assertDatabaseHas('competition_form_assignments', [
            'competition_form_id' => $form->id,
            'student_id' => $assigned->id,
            'status' => AssignmentStatus::Assigned->value,
            'response_status' => CompetitionResponseStatus::Pending->value,
        ]);

        Storage::disk('local')->assertExists($form->pdf_path);

        $this->actingAs($assigned->user)
            ->get(route('student.competition-forms.show', $form))
            ->assertOk()
            ->assertSee('Punjab State Taekwondo Championship');

        $this->actingAs($other->user)
            ->get(route('student.competition-forms.show', $form))
            ->assertForbidden();
    }

    public function test_student_download_tracks_viewed_and_downloaded(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'District Open',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('district.pdf', 100, 'application/pdf'),
        ])->assertRedirect();

        $form = CompetitionForm::query()->first();

        $this->actingAs($student->user)
            ->get(route('student.competition-forms.show', $form))
            ->assertOk();

        $assignment = CompetitionFormAssignment::query()->first();
        $this->assertNotNull($assignment->viewed_at);
        $this->assertNull($assignment->downloaded_at);

        $this->actingAs($student->user)
            ->get(route('student.competition-forms.download', $form))
            ->assertOk();

        $assignment->refresh();
        $this->assertNotNull($assignment->downloaded_at);
    }

    public function test_unassigned_student_cannot_download_pdf(): void
    {
        $admin = User::factory()->admin()->create();
        $assigned = Student::factory()->create();
        $other = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'State Meet',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$assigned->id],
            'pdf' => UploadedFile::fake()->create('state.pdf', 100, 'application/pdf'),
        ]);

        $form = CompetitionForm::query()->first();

        $this->actingAs($other->user)
            ->get(route('student.competition-forms.download', $form))
            ->assertForbidden();
    }

    public function test_pdf_is_not_publicly_accessible_by_path(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'Private Form',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('private.pdf', 100, 'application/pdf'),
        ]);

        $form = CompetitionForm::query()->first();

        $this->get('/storage/'.$form->pdf_path)->assertNotFound();
    }

    public function test_admin_can_update_response_status(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'Response Track',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('track.pdf', 100, 'application/pdf'),
        ]);

        $form = CompetitionForm::query()->first();
        $assignment = $form->assignments()->first();

        $this->actingAs($admin)
            ->post(route('admin.competition-forms.assignments.update', [$form, $assignment]), [
                'response_status' => CompetitionResponseStatus::Responded->value,
            ])
            ->assertRedirect();

        $this->assertSame(CompetitionResponseStatus::Responded, $assignment->fresh()->response_status);
    }

    public function test_inactive_form_hidden_from_student_list(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'Inactive Meet',
            'status' => AccountStatus::Inactive->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('inactive.pdf', 100, 'application/pdf'),
        ]);

        $this->actingAs($student->user)
            ->get(route('student.competition-forms.index'))
            ->assertOk()
            ->assertDontSee('Inactive Meet');
    }

    public function test_assigned_form_appears_on_student_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $student = Student::factory()->create();

        $this->actingAs($admin)->post(route('admin.competition-forms.store'), [
            'title' => 'Dashboard Meet',
            'status' => AccountStatus::Active->value,
            'student_ids' => [$student->id],
            'pdf' => UploadedFile::fake()->create('dash.pdf', 100, 'application/pdf'),
        ]);

        $this->actingAs($student->user)
            ->get(route('student.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Meet');
    }
}
