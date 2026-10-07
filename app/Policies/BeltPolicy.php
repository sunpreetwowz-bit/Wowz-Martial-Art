<?php

namespace App\Policies;

use App\Models\Belt;
use App\Models\User;

class BeltPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Belt $belt): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Belt $belt): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Belt $belt): bool
    {
        return $user->isAdmin();
    }
}
