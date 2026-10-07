<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BeltTestApplication;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', BeltTestApplication::class);

        $applications = BeltTestApplication::query()
            ->with(['beltTest.targetBelt', 'targetBelt', 'latestPayment'])
            ->where('student_id', $request->user()->student->id)
            ->latest('submitted_at')
            ->paginate(15);

        return view('student.applications.index', compact('applications'));
    }

    public function show(Request $request, BeltTestApplication $application)
    {
        $this->authorize('view', $application);

        $application->load(['beltTest', 'currentBelt', 'targetBelt', 'payments', 'result.certificate']);

        return view('student.applications.show', compact('application'));
    }
}
