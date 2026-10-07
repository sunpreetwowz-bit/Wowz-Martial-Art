<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStudent();
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isStudent()
            && $user->student
            && $user->student->is($payment->student);
    }

    public function create(User $user): bool
    {
        return $user->isStudent() || $user->isAdmin();
    }

    public function update(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function checkout(User $user, Payment $payment): bool
    {
        return $this->view($user, $payment)
            && $user->isStudent()
            && ! $payment->method->isOffline();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }
}
