<?php

namespace App\Policies;

use App\Enums\AssignmentStatus;
use App\Models\CompetitionForm;
use App\Models\User;

class CompetitionFormPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function view(User $user, CompetitionForm $competitionForm): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isStudent() || ! $user->student) {
            return false;
        }

        return $competitionForm->assignments()
            ->where('student_id', $user->student->id)
            ->where('status', AssignmentStatus::Assigned)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CompetitionForm $competitionForm): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CompetitionForm $competitionForm): bool
    {
        return $user->isAdmin();
    }

    public function download(User $user, CompetitionForm $competitionForm): bool
    {
        return $this->view($user, $competitionForm);
    }

    public function respond(User $user, CompetitionForm $competitionForm): bool
    {
        return $user->isStudent() && $this->view($user, $competitionForm);
    }

    public function assignStudents(User $user, CompetitionForm $competitionForm): bool
    {
        return $user->isAdmin();
    }
}
