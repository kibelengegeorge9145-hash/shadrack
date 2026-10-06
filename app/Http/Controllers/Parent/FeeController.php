<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\StudentFee;

class FeeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $parent = $user->parent;
        $childIds = $parent->students()->pluck('students.id');

        $children = $parent->students()->with('schoolClass')->get();

        $studentFees = StudentFee::whereIn('student_id', $childIds)
            ->with(['student', 'feeStructure', 'academicYear', 'term'])
            ->get();

        $payments = Payment::whereIn('student_id', $childIds)
            ->with(['feeStructure', 'student'])
            ->latest('payment_date')
            ->get();

        $totalRequired = (float) $studentFees->sum('required_amount');
        $totalPaid = (float) $studentFees->sum('paid_amount');
        $totalBalance = (float) $studentFees->sum('balance');

        return view('parent.fees', compact(
            'parent',
            'children',
            'studentFees',
            'payments',
            'totalRequired',
            'totalPaid',
            'totalBalance',
        ));
    }
}
