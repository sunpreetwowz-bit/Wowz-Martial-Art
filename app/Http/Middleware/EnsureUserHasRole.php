<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403);
        }

        $allowed = collect($roles)
            ->map(fn (string $role) => UserRole::tryFrom($role))
            ->filter()
            ->all();

        $hasRole = collect($allowed)->contains(fn (UserRole $role) => $user->role === $role);

        if (! $hasRole) {
            abort(403, 'You are not authorized to access this area.');
        }

        return $next($request);
    }
}
