<?php

namespace App\Http\Controllers\Admin;

use App\Enums\AccountStatus;
use App\Enums\Gender;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreStudentRequest;
use App\Http\Requests\Admin\UpdateStudentRequest;
use App\Models\Belt;
use App\Models\Service;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->authorizeResource(Student::class, 'student');
        $this->studentService = $studentService;
    }

    public function index(Request $request)
    {
        $query = Student::query()->with(['user', 'currentBelt', 'primaryService']);

        if ($request->input('q')) {
            $search = '%'.$request->input('q').'%';

            $query->where(function ($q) use ($search) {
                $q->where('student_code', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        if ($request->input('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->input('belt_id')) {
            $query->where('current_belt_id', $request->input('belt_id'));
        }

        $students = $query->latest()->paginate(15);

        return view('admin.students.index', [
            'students' => $students,
            'belts' => Belt::query()->orderBy('rank_order')->get(),
            'statuses' => AccountStatus::cases(),
        ]);
    }

    public function create()
    {
        return view('admin.students.create', $this->formData());
    }

    public function store(StoreStudentRequest $request)
    {
        $result = $this->studentService->create($request->validated(), $request->user());

        return redirect()
            ->route('admin.students.show', $result['student'])
            ->with('success', 'Student created successfully.')
            ->with('temporary_password', $result['temporary_password']);
    }

    public function show(Student $student)
    {
        $student->load([
            'user',
            'currentBelt',
            'primaryService',
            'beltHistories.fromBelt',
            'beltHistories.toBelt',
            'beltHistories.creator',
        ]);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $student->load(['user', 'currentBelt', 'primaryService']);

        return view('admin.students.edit', array_merge($this->formData(), [
            'student' => $student,
        ]));
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $this->studentService->update($student, $request->validated(), $request->user());

        return redirect()
            ->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $this->studentService->deactivate($student, request()->user());
        $student->delete();

        return redirect()
            ->route('admin.students.index')
            ->with('success', 'Student deactivated and archived successfully.');
    }

    public function activate(Student $student)
    {
        $this->authorize('update', $student);
        $this->studentService->activate($student, request()->user());

        return back()->with('success', 'Student activated successfully.');
    }

    public function deactivate(Student $student)
    {
        $this->authorize('update', $student);
        $this->studentService->deactivate($student, request()->user());

        return back()->with('success', 'Student deactivated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function formData()
    {
        return [
            'belts' => Belt::query()->active()->orderBy('rank_order')->get(),
            'services' => Service::query()->active()->get(),
            'statuses' => AccountStatus::cases(),
            'genders' => Gender::cases(),
        ];
    }
}
