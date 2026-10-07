<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Enums\CompetitionResponseStatus;
use App\Models\CompetitionForm;
use App\Models\CompetitionFormAssignment;
use App\Models\Student;
use App\Models\User;
use App\Support\Slug;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Upload competition PDFs, assign students, track downloads / Google Form replies.
 */
class CompetitionFormService
{
    protected $auditLogger;
    protected $notifications;

    public function __construct(
        AuditLogger $auditLogger,
        NotificationService $notifications,
    )
    {
        $this->auditLogger = $auditLogger;
        $this->notifications = $notifications;
    }

    public function create(array $data, UploadedFile $pdf, User $admin) {
        return DB::transaction(function () use ($data, $pdf, $admin) {
            $form = CompetitionForm::query()->create([
                'title' => $data['title'],
                'slug' => Slug::unique($data['slug'] ?? $data['title'], 'competition_forms'),
                'description' => $data['description'] ?? null,
                'pdf_path' => $this->storePdf($pdf),
                'google_form_url' => $data['google_form_url'] ?? null,
                'deadline_at' => $data['deadline_at'] ?? null,
                'status' => $data['status'],
                'created_by' => $admin->id,
            ]);

            $this->syncAssignments($form, $data['student_ids'] ?? [], $admin);

            $this->auditLogger->log(
                'competition_form.created',
                $form,
                null,
                [
                    'title' => $form->title,
                    'student_ids' => $data['student_ids'] ?? [],
                ],
                $admin
            );

            return $form->fresh(['assignments']);
        });
    }

    public function update(CompetitionForm $form, array $data, User $admin, ?UploadedFile $pdf = null) {
        return DB::transaction(function () use ($form, $data, $admin, $pdf) {
            $old = $form->only(['title', 'deadline_at', 'status', 'pdf_path', 'google_form_url']);

            $updateData = [
                'title' => $data['title'],
                'slug' => Slug::unique($data['slug'] ?? $data['title'], 'competition_forms', 'slug', $form->id),
                'description' => $data['description'] ?? null,
                'google_form_url' => $data['google_form_url'] ?? null,
                'deadline_at' => $data['deadline_at'] ?? null,
                'status' => $data['status'],
            ];

            if ($pdf) {
                $updateData['pdf_path'] = $this->storePdf($pdf, $form->pdf_path);
            }

            $form->update($updateData);
            $this->syncAssignments($form, $data['student_ids'] ?? [], $admin);

            $this->auditLogger->log(
                'competition_form.updated',
                $form,
                $old,
                $form->only(['title', 'deadline_at', 'status', 'pdf_path', 'google_form_url']),
                $admin
            );

            return $form->fresh(['assignments']);
        });
    }

    public function delete(CompetitionForm $form, User $admin)
    {
        $path = $form->pdf_path;
        $title = $form->title;

        $form->delete();

        $this->auditLogger->log(
            'competition_form.deleted',
            $form,
            ['title' => $title, 'pdf_path' => $path],
            null,
            $admin
        );
    }

    /** Assign selected students. Unchecked students become revoked. */
    public function syncAssignments(CompetitionForm $form, array $studentIds, User $admin)
    {
        // Clean list of student ids
        $selectedIds = [];
        foreach ($studentIds as $id) {
            $selectedIds[] = (int) $id;
        }
        $selectedIds = array_values(array_unique($selectedIds));

        $existing = $form->assignments()->get()->keyBy('student_id');
        $newlyAssigned = [];

        foreach ($selectedIds as $studentId) {
            if ($existing->has($studentId)) {
                $assignment = $existing->get($studentId);

                // Re-assign if previously revoked
                if ($assignment->status !== AssignmentStatus::Assigned) {
                    $assignment->update([
                        'status' => AssignmentStatus::Assigned,
                        'assigned_by' => $admin->id,
                        'assigned_at' => now(),
                        'response_status' => $assignment->response_status ?? CompetitionResponseStatus::Pending,
                    ]);
                    $newlyAssigned[] = $studentId;
                }
            } else {
                CompetitionFormAssignment::query()->create([
                    'competition_form_id' => $form->id,
                    'student_id' => $studentId,
                    'status' => AssignmentStatus::Assigned,
                    'response_status' => CompetitionResponseStatus::Pending,
                    'assigned_by' => $admin->id,
                    'assigned_at' => now(),
                ]);
                $newlyAssigned[] = $studentId;
            }
        }

        // Revoke anyone who was assigned but is no longer selected
        foreach ($existing as $studentId => $assignment) {
            if (! in_array((int) $studentId, $selectedIds, true)) {
                if ($assignment->status === AssignmentStatus::Assigned) {
                    $assignment->update(['status' => AssignmentStatus::Revoked]);
                }
            }
        }

        $this->auditLogger->log(
            'competition_form.assigned',
            $form,
            null,
            ['student_ids' => $selectedIds],
            $admin
        );

        // Notify only newly assigned students
        if (count($newlyAssigned) > 0) {
            $students = Student::query()->with('user')->whereIn('id', $newlyAssigned)->get();
            foreach ($students as $student) {
                $this->notifications->competitionAssigned($student, $form);
            }
        }
    }

    public function assignedStudentIds(CompetitionForm $form) {
        return $form->assignments()
            ->where('status', AssignmentStatus::Assigned)
            ->pluck('student_id');
    }

    public function markViewed(CompetitionFormAssignment $assignment)
    {
        if ($assignment->viewed_at) {
            return;
        }

        $assignment->update(['viewed_at' => now()]);
    }

    public function markDownloaded(CompetitionFormAssignment $assignment)
    {
        $assignment->update([
            'downloaded_at' => now(),
            'viewed_at' => $assignment->viewed_at ?? now(),
        ]);
    }

    public function markResponded(CompetitionFormAssignment $assignment, User $studentUser)
    {
        if ($assignment->response_status === CompetitionResponseStatus::Responded) {
            return;
        }

        $assignment->update([
            'response_status' => CompetitionResponseStatus::Responded,
            'viewed_at' => $assignment->viewed_at ?? now(),
        ]);

        $assignment->loadMissing(['competitionForm', 'student.user']);

        $this->auditLogger->log(
            'competition_form.responded',
            $assignment->competitionForm,
            null,
            [
                'response_status' => 'responded',
                'student_id' => $assignment->student_id,
            ],
            $studentUser
        );

        $this->notifications->competitionResponded($assignment);
    }

    public function updateResponseStatus(
        CompetitionFormAssignment $assignment,
        string $status,
        User $admin,
    ) {
        $old = $assignment->response_status?->value;

        $assignment->update(['response_status' => $status]);

        $this->auditLogger->log(
            'competition_form.response_updated',
            $assignment->competitionForm,
            ['response_status' => $old, 'student_id' => $assignment->student_id],
            ['response_status' => $status, 'student_id' => $assignment->student_id],
            $admin
        );
    }

    protected function storePdf(UploadedFile $file, ?string $existing = null)
    {
        if ($existing) {
            Storage::disk('local')->delete($existing);
        }

        return $file->store('competition-forms', 'local');
    }
}
