<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ApplicationStatus;
use App\Enums\TestResultOutcome;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBeltTestResultRequest;
use App\Http\Requests\Admin\UpdateBeltTestResultRequest;
use App\Models\BeltTestApplication;
use App\Models\BeltTestResult;
use App\Services\BeltProgressionService;
use Illuminate\Http\Request;

class BeltTestResultController extends Controller
{
    protected $progressionService;

    public function __construct(BeltProgressionService $progressionService)
    {
        $this->authorizeResource(BeltTestResult::class, 'result');
        $this->progressionService = $progressionService;
    }

    public function index(Request $request)
    {
        $query = BeltTestResult::query()->with(['student.user', 'beltTest', 'application']);

        if ($request->input('outcome')) {
            $query->where('outcome', $request->input('outcome'));
        }

        if ($request->input('q')) {
            $search = '%'.$request->input('q').'%';

            $query->where(function ($q) use ($search) {
                $q->whereHas('student.user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', $search);
                })->orWhereHas('beltTest', function ($testQuery) use ($search) {
                    $testQuery->where('title', 'like', $search);
                });
            });
        }

        $results = $query->latest()->paginate(20);

        return view('admin.belt-test-results.index', [
            'results' => $results,
            'outcomes' => TestResultOutcome::cases(),
        ]);
    }

    public function create(Request $request)
    {
        $application = null;
        if ($request->input('application')) {
            $application = BeltTestApplication::query()
                ->with(['student.user', 'beltTest', 'targetBelt', 'result'])
                ->findOrFail($request->integer('application'));
        }

        $eligible = BeltTestApplication::query()
            ->with(['student.user', 'beltTest', 'targetBelt'])
            ->whereDoesntHave('result')
            ->whereIn('status', [
                ApplicationStatus::Submitted,
                ApplicationStatus::PaymentPending,
                ApplicationStatus::PaymentVerified,
                ApplicationStatus::UnderReview,
                ApplicationStatus::Accepted,
            ])
            ->latest('submitted_at')
            ->limit(100)
            ->get();

        return view('admin.belt-test-results.create', [
            'application' => $application,
            'eligible' => $eligible,
            'outcomes' => TestResultOutcome::cases(),
        ]);
    }

    public function store(StoreBeltTestResultRequest $request)
    {
        $data = $request->validated();
        $application = BeltTestApplication::query()->findOrFail($data['belt_test_application_id']);

        try {
            $result = $this->progressionService->recordResult($application, $data, $request->user());
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()
            ->route('admin.belt-test-results.show', $result)
            ->with('success', 'Result recorded successfully.');
    }

    public function show(BeltTestResult $result)
    {
        $result->load([
            'student.user',
            'beltTest',
            'application.targetBelt',
            'application.currentBelt',
            'certificate',
            'recorder',
            'updater',
        ]);

        return view('admin.belt-test-results.show', [
            'result' => $result,
            'outcomes' => TestResultOutcome::cases(),
        ]);
    }

    public function update(UpdateBeltTestResultRequest $request, BeltTestResult $result)
    {
        try {
            $this->progressionService->correctResult($result, $request->validated(), $request->user());
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Result corrected successfully. Audit log updated.');
    }
}
