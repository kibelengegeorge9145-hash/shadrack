<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

class FeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        $studentFees = $student->studentFees()
            ->with(['feeStructure', 'academicYear', 'term'])
            ->get();

        $payments = $student->payments()
            ->with(['feeStructure'])
            ->latest('payment_date')
            ->get();

        $totalRequired = (float) $studentFees->sum('required_amount');
        $totalPaid = (float) $studentFees->sum('paid_amount');
        $totalBalance = (float) $studentFees->sum('balance');

        return view('student.fees', compact(
            'student',
            'studentFees',
            'payments',
            'totalRequired',
            'totalPaid',
            'totalBalance',
        ));
    }
}
