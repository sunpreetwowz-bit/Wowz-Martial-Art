<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent() && $user->student?->is($student);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function updateOfficialRecords(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }
}
