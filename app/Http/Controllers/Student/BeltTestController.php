<?php

namespace App\Http\Controllers\Student;

use App\Enums\AssignmentStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Exceptions\BeltTestAccessException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\SubmitBeltTestApplicationRequest;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Services\BeltTestAccessService;
use App\Services\BeltTestApplicationService;
use Illuminate\Http\Request;

/**
 * Student belt-test list, detail, and apply form.
 */
class BeltTestController extends Controller
{
    protected $accessService;
    protected $applicationService;

    public function __construct(
        BeltTestAccessService $accessService,
        BeltTestApplicationService $applicationService,
    )
    {
        $this->accessService = $accessService;
        $this->applicationService = $applicationService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', BeltTest::class);

        $student = $request->user()->student;

        $tests = BeltTest::query()
            ->with('targetBelt')
            ->whereHas('assignments', function ($query) use ($student) {
                $query->where('student_id', $student->id)
                    ->where('status', AssignmentStatus::Assigned);
            })
            ->with(['applications' => function ($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->orderBy('test_date')
            ->get();

        // Add a few helper fields for the Blade view
        foreach ($tests as $test) {
            $test->can_apply = $this->accessService->canApply($student, $test);
            $test->closes_at = $test->effectiveApplicationClosesAt();
            $test->my_application = $test->applications->first();
        }

        return view('student.belt-tests.index', compact('tests'));
    }

    public function show(Request $request, BeltTest $beltTest)
    {
        $this->authorize('view', $beltTest);

        $student = $request->user()->student;

        $application = BeltTestApplication::query()
            ->with('latestPayment')
            ->where('belt_test_id', $beltTest->id)
            ->where('student_id', $student->id)
            ->first();

        return view('student.belt-tests.show', [
            'test' => $beltTest->load('targetBelt'),
            'application' => $application,
            'canApply' => $this->accessService->canApply($student, $beltTest),
            'closesAt' => $beltTest->effectiveApplicationClosesAt(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function applyForm(Request $request, BeltTest $beltTest)
    {
        $this->authorize('apply', $beltTest);

        $student = $request->user()->student;

        try {
            $this->accessService->assertCanApply($student, $beltTest);
        } catch (BeltTestAccessException $e) {
            return redirect()
                ->route('student.belt-tests.show', $beltTest)
                ->with('error', $e->getMessage());
        }

        return view('student.belt-tests.apply', [
            'test' => $beltTest->load('targetBelt'),
            'student' => $student->load('currentBelt'),
            'closesAt' => $beltTest->effectiveApplicationClosesAt(),
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function apply(SubmitBeltTestApplicationRequest $request, BeltTest $beltTest)
    {
        $student = $request->user()->student;
        $paymentMethod = $request->validated('payment_method');

        try {
            $application = $this->applicationService->submit(
                $student,
                $beltTest,
                $paymentMethod,
                [
                    'notes' => $request->validated('notes'),
                    'emergency_contact' => $request->validated('emergency_contact'),
                    'acknowledge' => true,
                ],
                'app-'.$beltTest->id.'-'.$student->id
            );
        } catch (BeltTestAccessException $e) {
            return back()->with('error', $e->getMessage());
        }

        $payment = $application->latestPayment;

        // Online methods go to sandbox checkout
        if ($payment && ! $payment->method->isOffline() && $payment->status === PaymentStatus::Pending) {
            return redirect()
                ->route('student.payments.checkout', $payment)
                ->with('success', 'Application submitted. Complete sandbox checkout to finish payment.');
        }

        return redirect()
            ->route('student.applications.show', $application)
            ->with('success', 'Application submitted successfully. It can no longer be edited.');
    }
}
