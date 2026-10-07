<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\AssignmentStatus;
use App\Enums\CompetitionResponseStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCompetitionFormRequest;
use App\Http\Requests\Admin\UpdateCompetitionFormRequest;
use App\Models\CompetitionForm;
use App\Models\CompetitionFormAssignment;
use App\Models\Student;
use App\Services\CompetitionFormService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CompetitionFormController extends Controller
{
    protected $formService;

    public function __construct(CompetitionFormService $formService)
    {
        $this->authorizeResource(CompetitionForm::class, 'competition_form');
        $this->formService = $formService;
    }

    public function index(Request $request)
    {
        $query = CompetitionForm::query()
            ->withCount([
                'assignments as assigned_count' => function ($q) {
                    $q->where('status', AssignmentStatus::Assigned);
                },
            ]);

        if ($request->input('q')) {
            $query->where('title', 'like', '%'.$request->input('q').'%');
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        $forms = $query->latest()->paginate(15);

        return view('admin.competition-forms.index', [
            'forms' => $forms,
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.competition-forms.create', $this->formData());
    }

    public function store(StoreCompetitionFormRequest $request)
    {
        $form = $this->formService->create(
            $request->safe()->except(['pdf']),
            $request->file('pdf'),
            $request->user(),
        );

        return redirect()
            ->route('admin.competition-forms.show', $form)
            ->with('success', 'Competition form created and assigned.');
    }

    public function show(CompetitionForm $competition_form)
    {
        $competition_form->load([
            'creator',
            'assignments.student.user',
        ]);

        return view('admin.competition-forms.show', [
            'form' => $competition_form,
            'responseStatuses' => CompetitionResponseStatus::cases(),
        ]);
    }

    public function edit(CompetitionForm $competition_form)
    {
        return view('admin.competition-forms.edit', array_merge($this->formData(), [
            'form' => $competition_form,
            'selectedStudentIds' => $this->formService->assignedStudentIds($competition_form)->all(),
        ]));
    }

    public function update(UpdateCompetitionFormRequest $request, CompetitionForm $competition_form)
    {
        $this->formService->update(
            $competition_form,
            $request->safe()->except(['pdf']),
            $request->user(),
            $request->file('pdf'),
        );

        return redirect()
            ->route('admin.competition-forms.show', $competition_form)
            ->with('success', 'Competition form updated.');
    }

    public function destroy(CompetitionForm $competition_form)
    {
        $this->formService->delete($competition_form, request()->user());

        return redirect()
            ->route('admin.competition-forms.index')
            ->with('success', 'Competition form deleted.');
    }

    public function download(CompetitionForm $competition_form)
    {
        $this->authorize('download', $competition_form);

        if (! Storage::disk('local')->exists($competition_form->pdf_path)) {
            abort(404);
        }

        $fileName = Str::slug($competition_form->title).'.pdf';

        return Storage::disk('local')->download($competition_form->pdf_path, $fileName);
    }

    public function updateAssignment(
        Request $request,
        CompetitionForm $competition_form,
        CompetitionFormAssignment $assignment
    ) {
        $this->authorize('assignStudents', $competition_form);

        if ($assignment->competition_form_id !== $competition_form->id) {
            abort(404);
        }

        $data = $request->validate([
            'response_status' => ['required', Rule::enum(CompetitionResponseStatus::class)],
        ]);

        $this->formService->updateResponseStatus(
            $assignment,
            $data['response_status'],
            $request->user()
        );

        return back()->with('success', 'Assignment response status updated.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function formData()
    {
        return [
            'statuses' => AccountStatus::cases(),
            'students' => Student::query()
                ->with('user')
                ->where('status', AccountStatus::Active)
                ->orderBy('student_code')
                ->get(),
        ];
    }
}
