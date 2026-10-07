<?php

namespace App\Http\Controllers\Student;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Http\Controllers\Controller;
use App\Models\BeltTest;
use App\Models\CompetitionForm;
use App\Services\BeltTestAccessService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, BeltTestAccessService $accessService)
    {
        $student = $request->user()->student;

        if (! $student) {
            abort(404);
        }

        $student->load([
            'currentBelt',
            'primaryService',
            'beltHistories.fromBelt',
            'beltHistories.toBelt',
            'applications.beltTest',
            'applications.latestPayment',
        ]);

        // Keep only the latest few histories / applications for the dashboard
        $student->setRelation(
            'beltHistories',
            $student->beltHistories->sortByDesc('id')->take(5)->values()
        );
        $student->setRelation(
            'applications',
            $student->applications->sortByDesc('submitted_at')->take(5)->values()
        );

        $upcomingTests = BeltTest::query()
            ->with('targetBelt')
            ->whereDate('test_date', '>=', now()->toDateString())
            ->whereHas('assignments', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->where('status', AssignmentStatus::Assigned);
            })
            ->orderBy('test_date')
            ->limit(5)
            ->get();

        foreach ($upcomingTests as $test) {
            $test->can_apply = $accessService->canApply($student, $test);
            $test->closes_at = $test->effectiveApplicationClosesAt();
        }

        $competitionForms = CompetitionForm::query()
            ->where('status', AccountStatus::Active)
            ->whereHas('assignments', function ($q) use ($student) {
                $q->where('student_id', $student->id)
                    ->where('status', AssignmentStatus::Assigned);
            })
            ->orderBy('deadline_at')
            ->limit(5)
            ->get();

        return view('student.dashboard', [
            'student' => $student,
            'upcomingTests' => $upcomingTests,
            'competitionForms' => $competitionForms,
        ]);
    }
}
