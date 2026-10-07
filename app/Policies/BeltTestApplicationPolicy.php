<?php

namespace App\Policies;

use App\Models\BeltTestApplication;
use App\Models\User;

class BeltTestApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function view(User $user, BeltTestApplication $application): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent()
            && $user->student
            && $user->student->is($application->student);
    }

    public function create(User $user): bool
    {
        return $user->isStudent();
    }

    public function update(User $user, BeltTestApplication $application): bool
    {
        // Students cannot modify submitted applications.
        return $user->isAdmin();
    }

    public function delete(User $user, BeltTestApplication $application): bool
    {
        return $user->isAdmin();
    }

    public function review(User $user, BeltTestApplication $application): bool
    {
        return $user->isAdmin();
    }
}
