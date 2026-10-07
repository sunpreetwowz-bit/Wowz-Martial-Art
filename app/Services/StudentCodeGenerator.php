<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Support\Facades\DB;

class StudentCodeGenerator
{
    public function generate(?int $year = null)
    {
        $year ??= (int) now(config('app.timezone'))->format('Y');
        $prefix = config('academy.student_code_prefix', 'WMA');

        return DB::transaction(function () use ($prefix, $year) {
            $latest = Student::withTrashed()
                ->where('student_code', 'like', "{$prefix}-{$year}-%")
                ->orderByDesc('student_code')
                ->lockForUpdate()
                ->value('student_code');

            $next = 1;

            if ($latest && preg_match('/-(\d+)$/', $latest, $matches)) {
                $next = ((int) $matches[1]) + 1;
            }

            return sprintf('%s-%d-%04d', $prefix, $year, $next);
        });
    }
}
