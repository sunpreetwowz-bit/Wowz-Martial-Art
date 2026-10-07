<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Models\BeltTest;
use App\Models\BeltTestAssignment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Assign / revoke students on a belt test.
 */
class BeltTestAssignmentService
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

    public function syncAssignments(BeltTest $beltTest, array $studentIds, User $admin)
    {
        DB::transaction(function () use ($beltTest, $studentIds, $admin) {
            $selectedIds = [];
            foreach ($studentIds as $id) {
                $selectedIds[] = (int) $id;
            }
            $selectedIds = array_values(array_unique($selectedIds));

            $existing = $beltTest->assignments()->get()->keyBy('student_id');
            $newlyAssigned = [];

            foreach ($selectedIds as $studentId) {
                if ($existing->has($studentId)) {
                    $assignment = $existing->get($studentId);

                    if ($assignment->status !== AssignmentStatus::Assigned) {
                        $assignment->update([
                            'status' => AssignmentStatus::Assigned,
                            'assigned_by' => $admin->id,
                            'assigned_at' => now(),
                            'revoked_at' => null,
                        ]);
                        $newlyAssigned[] = $studentId;
                    }
                } else {
                    BeltTestAssignment::query()->create([
                        'belt_test_id' => $beltTest->id,
                        'student_id' => $studentId,
                        'status' => AssignmentStatus::Assigned,
                        'assigned_by' => $admin->id,
                        'assigned_at' => now(),
                    ]);
                    $newlyAssigned[] = $studentId;
                }
            }

            foreach ($existing as $studentId => $assignment) {
                if (! in_array((int) $studentId, $selectedIds, true)) {
                    if ($assignment->status === AssignmentStatus::Assigned) {
                        $assignment->update([
                            'status' => AssignmentStatus::Revoked,
                            'revoked_at' => now(),
                        ]);
                    }
                }
            }

            $this->auditLogger->log(
                'belt_test.assignments_synced',
                $beltTest,
                null,
                ['student_ids' => $selectedIds],
                $admin
            );

            if (count($newlyAssigned) > 0) {
                $students = Student::query()->with('user')->whereIn('id', $newlyAssigned)->get();
                foreach ($students as $student) {
                    $this->notifications->beltTestAssigned($student, $beltTest);
                }
            }
        });
    }

    public function assignedStudentIds(BeltTest $beltTest) {
        return $beltTest->assignments()
            ->where('status', AssignmentStatus::Assigned)
            ->pluck('student_id');
    }
}
