<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Http\Controllers\Controller;
use App\Models\BeltTestApplication;
use App\Services\BeltTestApplicationService;
use Illuminate\Http\Request;
use RuntimeException;

class BeltTestApplicationController extends Controller
{
    protected $applicationService;

    public function __construct(BeltTestApplicationService $applicationService)
    {
        $this->applicationService = $applicationService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', BeltTestApplication::class);

        $query = BeltTestApplication::query()
            ->with(['student.user', 'beltTest', 'targetBelt', 'latestPayment']);

        // Filter by status
        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        // Search by student name, student code, or test title
        if ($request->input('q')) {
            $search = '%'.$request->input('q').'%';

            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', $search);
                })
                ->orWhereHas('student', function ($studentQuery) use ($search) {
                    $studentQuery->where('student_code', 'like', $search);
                })
                ->orWhereHas('beltTest', function ($testQuery) use ($search) {
                    $testQuery->where('title', 'like', $search);
                });
            });
        }

        $applications = $query->latest('submitted_at')->paginate(20);

        return view('admin.belt-test-applications.index', [
            'applications' => $applications,
            'statuses' => ApplicationStatus::cases(),
        ]);
    }

    public function show(BeltTestApplication $application)
    {
        $this->authorize('view', $application);

        $application->load([
            'student.user',
            'beltTest.targetBelt',
            'currentBelt',
            'targetBelt',
            'payments',
            'result',
            'reviewer',
        ]);

        return view('admin.belt-test-applications.show', [
            'application' => $application,
            'reviewStatuses' => ApplicationStatus::adminReviewOptions(),
        ]);
    }

    public function review(Request $request, BeltTestApplication $application)
    {
        $this->authorize('review', $application);

        // Build a simple list of allowed status values
        $allowed = [];
        foreach (ApplicationStatus::adminReviewOptions() as $status) {
            $allowed[] = $status->value;
        }

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', $allowed)],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $this->applicationService->review(
                $application,
                $data['status'],
                $request->user(),
                $data['admin_notes'] ?? null
            );
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Application updated.');
    }
}
