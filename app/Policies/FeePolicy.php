<?php

namespace App\Policies;

use App\Models\User;

class FeePolicy
{
    public function manage(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }

    public function viewFinances(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }

    public function assign(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }

    public function recordPayment(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }

    public function generateReceipt(User $user): bool
    {
        return $user->hasRole('admin', 'accountant');
    }
}
