<?php

namespace App\Http\Controllers\Student;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Http\Controllers\Controller;
use App\Models\CompetitionForm;
use App\Models\CompetitionFormAssignment;
use App\Services\CompetitionFormService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompetitionFormController extends Controller
{
    protected $formService;

    public function __construct(CompetitionFormService $formService)
    {
        $this->formService = $formService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', CompetitionForm::class);

        $studentId = $request->user()->student->id;

        $forms = CompetitionForm::query()
            ->where('status', AccountStatus::Active)
            ->whereHas('assignments', function ($q) use ($studentId) {
                $q->where('student_id', $studentId)
                    ->where('status', AssignmentStatus::Assigned);
            })
            ->with(['assignments' => function ($q) use ($studentId) {
                $q->where('student_id', $studentId);
            }])
            ->orderBy('deadline_at')
            ->paginate(15);

        return view('student.competition-forms.index', [
            'forms' => $forms,
        ]);
    }

    public function show(Request $request, CompetitionForm $competitionForm)
    {
        $this->authorize('view', $competitionForm);

        $assignment = $this->assignmentFor($request, $competitionForm);
        $this->formService->markViewed($assignment);

        return view('student.competition-forms.show', [
            'form' => $competitionForm,
            'assignment' => $assignment->fresh(),
        ]);
    }

    public function download(Request $request, CompetitionForm $competitionForm)
    {
        $this->authorize('download', $competitionForm);

        if (! $competitionForm->isActive()) {
            abort(403);
        }

        if (! Storage::disk('local')->exists($competitionForm->pdf_path)) {
            abort(404);
        }

        $assignment = $this->assignmentFor($request, $competitionForm);
        $this->formService->markDownloaded($assignment);

        $fileName = Str::slug($competitionForm->title).'.pdf';

        return Storage::disk('local')->download($competitionForm->pdf_path, $fileName);
    }

    public function markResponded(Request $request, CompetitionForm $competitionForm)
    {
        $this->authorize('respond', $competitionForm);

        if (! $competitionForm->isActive()) {
            abort(403);
        }

        if (empty($competitionForm->google_form_url)) {
            abort(403);
        }

        $assignment = $this->assignmentFor($request, $competitionForm);
        $this->formService->markResponded($assignment, $request->user());

        return redirect()
            ->route('student.competition-forms.show', $competitionForm)
            ->with('success', 'Marked as responded. Thank you.');
    }

    protected function assignmentFor(Request $request, CompetitionForm $form)
    {
        return CompetitionFormAssignment::query()
            ->where('competition_form_id', $form->id)
            ->where('student_id', $request->user()->student->id)
            ->where('status', AssignmentStatus::Assigned)
            ->firstOrFail();
    }
}
