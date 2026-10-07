<?php

namespace App\Services;

use App\Enums\AssignmentStatus;
use App\Enums\BeltTestStatus;
use App\Exceptions\BeltTestAccessException;
use App\Models\BeltTest;
use App\Models\BeltTestApplication;
use App\Models\Student;

/**
 * Simple checks: can this student apply to this belt test right now?
 */
class BeltTestAccessService
{
    public function isAssigned(Student $student, BeltTest $beltTest)
    {
        return $beltTest->assignments()
            ->where('student_id', $student->id)
            ->where('status', AssignmentStatus::Assigned)
            ->exists();
    }

    public function hasApplication(Student $student, BeltTest $beltTest)
    {
        return BeltTestApplication::query()
            ->where('belt_test_id', $beltTest->id)
            ->where('student_id', $student->id)
            ->exists();
    }

    public function isWithinApplicationWindow(BeltTest $beltTest)
    {
        $now = now(config('app.timezone'));

        // Too early
        if ($beltTest->application_opens_at && $now->lt($beltTest->application_opens_at)) {
            return false;
        }

        // Too late (closed at or after the close time)
        return $now->lt($beltTest->effectiveApplicationClosesAt());
    }

    public function testAcceptsApplications(BeltTest $beltTest)
    {
        return in_array($beltTest->status, [
            BeltTestStatus::Scheduled,
            BeltTestStatus::Open,
        ], true);
    }

    /** Throw a clear error if the student cannot apply. */
    public function assertCanApply(Student $student, BeltTest $beltTest)
    {
        if (! $student->isActive()) {
            throw BeltTestAccessException::inactiveStudent();
        }

        if (! $this->isAssigned($student, $beltTest)) {
            throw BeltTestAccessException::notAssigned();
        }

        if (! $this->testAcceptsApplications($beltTest)) {
            throw BeltTestAccessException::testUnavailable();
        }

        $now = now(config('app.timezone'));

        if ($beltTest->application_opens_at && $now->lt($beltTest->application_opens_at)) {
            throw BeltTestAccessException::notOpenYet();
        }

        if (! $this->isWithinApplicationWindow($beltTest)) {
            throw BeltTestAccessException::closed();
        }

        if ($this->hasApplication($student, $beltTest)) {
            throw BeltTestAccessException::alreadySubmitted();
        }
    }

    /** True/false version of assertCanApply (no exception). */
    public function canApply(Student $student, BeltTest $beltTest)
    {
        try {
            $this->assertCanApply($student, $beltTest);

            return true;
        } catch (BeltTestAccessException) {
            return false;
        }
    }
}
