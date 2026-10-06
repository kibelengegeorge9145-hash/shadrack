<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Str;

class AuthService
{
    public function nextAdmissionNumber(): string
    {
        $year = now()->format('Y');
        $prefix = 'EBSS-'.$year.'-';
        $last = Student::orderBy('id', 'desc')->value('admission_number');
        $sequence = 1;

        if ($last && str_starts_with($last, $prefix)) {
            $sequence = ((int) substr($last, strlen($prefix))) + 1;
        }

        return $prefix.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }

    public function nextStaffId(string $prefix = 'EBT'): string
    {
        $last = Teacher::orderBy('id', 'desc')->value('staff_id');
        $sequence = 1;

        if ($last && str_starts_with($last, $prefix)) {
            $sequence = ((int) substr($last, strlen($prefix))) + 1;
        }

        return $prefix.str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    public function temporaryPassword(int $length = 8): string
    {
        $password = Str::password($length);

        // Ensure it contains a symbol/digit to satisfy common policies.
        return $password;
    }
}
