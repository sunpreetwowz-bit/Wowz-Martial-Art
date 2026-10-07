<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function view(User $user, Certificate $certificate): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent()
            && $user->student
            && $user->student->is($certificate->student);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }

    public function revoke(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Certificate $certificate): bool
    {
        return $user->isAdmin();
    }

    public function download(User $user, Certificate $certificate): bool
    {
        return $this->view($user, $certificate);
    }
}
