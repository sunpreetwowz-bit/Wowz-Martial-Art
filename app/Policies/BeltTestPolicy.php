<?php

namespace App\Policies;

use App\Enums\AssignmentStatus;
use App\Models\BeltTest;
use App\Models\User;

class BeltTestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function view(User $user, BeltTest $beltTest): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isStudent() || ! $user->student) {
            return false;
        }

        return $beltTest->assignments()
            ->where('student_id', $user->student->id)
            ->where('status', AssignmentStatus::Assigned)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BeltTest $beltTest): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BeltTest $beltTest): bool
    {
        return $user->isAdmin();
    }

    public function assignStudents(User $user, BeltTest $beltTest): bool
    {
        return $user->isAdmin();
    }

    public function apply(User $user, BeltTest $beltTest): bool
    {
        return $this->view($user, $beltTest);
    }
}
