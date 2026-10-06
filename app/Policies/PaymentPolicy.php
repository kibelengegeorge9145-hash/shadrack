<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->hasRole('admin', 'accountant')) {
            return true;
        }
        // Parents / students can see payments for their own student.
        if ($user->isParent()) {
            return $payment->student?->parent_id === $user->parent?->id;
        }
        if ($user->isStudent()) {
            return $payment->student_id === $user->student?->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }

    public function update(User $user, Payment $payment): bool
    {
        // Accountant may edit permitted, non-reversed payment records.
        return $user->hasRole('admin', 'accountant') && $payment->status !== 'reversed';
    }

    public function reverse(User $user, Payment $payment): bool
    {
        return $user->hasRole('admin', 'accountant') && $payment->status === 'recorded';
    }
}
