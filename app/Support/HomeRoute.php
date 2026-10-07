<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

/**
 * Decide where a logged-in user should go after login.
 */
class HomeRoute
{
    public static function for(?User $user): string
    {
        if (! $user) {
            return route('login');
        }

        if ($user->role === UserRole::Admin) {
            return route('admin.dashboard');
        }

        if ($user->role === UserRole::Student) {
            return route('student.dashboard');
        }

        return route('login');
    }

    public static function pathFor(?User $user): string
    {
        if (! $user) {
            return '/login';
        }

        if ($user->role === UserRole::Admin) {
            return '/admin/dashboard';
        }

        if ($user->role === UserRole::Student) {
            return '/student/dashboard';
        }

        return '/login';
    }
}
