<?php

namespace App\Policies;

use App\Models\BeltTestResult;
use App\Models\User;

class BeltTestResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function view(User $user, BeltTestResult $result): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent()
            && $user->student
            && $user->student->is($result->student);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, BeltTestResult $result): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, BeltTestResult $result): bool
    {
        return $user->isAdmin();
    }
}
