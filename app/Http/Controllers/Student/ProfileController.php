<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $student = $request->user()->student;

        if (! $student) {
            abort(404);
        }

        $this->authorize('view', $student);

        $student->load([
            'currentBelt',
            'primaryService',
            'beltHistories.fromBelt',
            'beltHistories.toBelt',
        ]);

        return view('student.profile', [
            'student' => $student,
        ]);
    }
}
