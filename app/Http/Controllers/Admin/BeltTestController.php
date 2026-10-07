<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\BeltTestStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBeltTestRequest;
use App\Http\Requests\Admin\UpdateBeltTestRequest;
use App\Models\Belt;
use App\Models\BeltTest;
use App\Models\Student;
use App\Services\AuditLogger;
use App\Services\BeltTestAssignmentService;
use App\Support\Slug;
use Illuminate\Http\Request;

class BeltTestController extends Controller
{
    protected $assignmentService;
    protected $auditLogger;

    public function __construct(
        BeltTestAssignmentService $assignmentService,
        AuditLogger $auditLogger,
    )
    {
        $this->authorizeResource(BeltTest::class, 'belt_test');
        $this->assignmentService = $assignmentService;
        $this->auditLogger = $auditLogger;
    }

    public function index(Request $request)
    {
        $query = BeltTest::query()
            ->with('targetBelt')
            ->withCount([
                'assignments as assigned_count' => function ($q) {
                    $q->where('status', 'assigned');
                },
                'applications',
            ]);

        if ($request->input('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $tests = $query->orderByDesc('test_date')->paginate(15);

        return view('admin.belt-tests.index', [
            'tests' => $tests,
            'statuses' => BeltTestStatus::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.belt-tests.create', $this->formData());
    }

    public function store(StoreBeltTestRequest $request)
    {
        $data = $request->safe()->except(['student_ids']);
        $data['slug'] = Slug::unique($data['slug'] ?? $data['title'], 'belt_tests');
        $data['currency'] = $data['currency'] ?? 'INR';
        $data['created_by'] = $request->user()->id;

        $test = BeltTest::query()->create($data);

        $this->assignmentService->syncAssignments(
            $test,
            $request->input('student_ids', []),
            $request->user()
        );

        $this->auditLogger->log('belt_test.created', $test, null, $test->toArray(), $request->user());

        return redirect()
            ->route('admin.belt-tests.show', $test)
            ->with('success', 'Belt test created successfully.');
    }

    public function show(BeltTest $belt_test)
    {
        $belt_test->load([
            'targetBelt',
            'assignments.student.user',
            'applications.student.user',
            'applications.latestPayment',
        ]);

        return view('admin.belt-tests.show', [
            'test' => $belt_test,
            'closesAt' => $belt_test->effectiveApplicationClosesAt(),
        ]);
    }

    public function edit(BeltTest $belt_test)
    {
        return view('admin.belt-tests.edit', array_merge($this->formData(), [
            'test' => $belt_test,
            'selectedStudentIds' => $this->assignmentService->assignedStudentIds($belt_test)->all(),
        ]));
    }

    public function update(UpdateBeltTestRequest $request, BeltTest $belt_test)
    {
        $data = $request->safe()->except(['student_ids']);
        $data['slug'] = Slug::unique($data['slug'] ?? $data['title'], 'belt_tests', 'slug', $belt_test->id);
        $data['currency'] = $data['currency'] ?? 'INR';

        $belt_test->update($data);

        $this->assignmentService->syncAssignments(
            $belt_test,
            $request->input('student_ids', []),
            $request->user()
        );

        $this->auditLogger->log('belt_test.updated', $belt_test, null, null, $request->user());

        return redirect()
            ->route('admin.belt-tests.show', $belt_test)
            ->with('success', 'Belt test updated successfully.');
    }

    public function destroy(BeltTest $belt_test)
    {
        $belt_test->update(['status' => BeltTestStatus::Cancelled]);
        $belt_test->delete();

        return redirect()
            ->route('admin.belt-tests.index')
            ->with('success', 'Belt test cancelled and archived.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function formData()
    {
        return [
            'belts' => Belt::query()->active()->orderBy('rank_order')->get(),
            'students' => Student::query()
                ->with('user')
                ->where('status', AccountStatus::Active)
                ->orderBy('student_code')
                ->get(),
            'statuses' => BeltTestStatus::cases(),
        ];
    }
}
