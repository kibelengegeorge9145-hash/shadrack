<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;

class DashboardController extends Controller
{
    public function index()
    {
        $year = AcademicYear::where('is_current', true)->first();
        $term = $year ? Term::where('is_current', true)->where('academic_year_id', $year->id)->first() : null;

        $yearId = $year?->id;
        $termId = $term?->id;

        $collected = Payment::where('status', 'recorded')
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->sum('amount');

        $summary = (object) [
            'expected' => (float) StudentFee::when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
                ->when($termId, fn ($q) => $q->where('term_id', $termId))
                ->sum('required_amount'),
            'outstanding' => (float) StudentFee::when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
                ->when($termId, fn ($q) => $q->where('term_id', $termId))
                ->sum('balance'),
        ];

        $totalStudents = Student::active()->count();

        $monthly = Payment::where('status', 'recorded')
            ->whereYear('payment_date', now()->year)
            ->selectRaw("DATE_FORMAT(payment_date, '%Y-%m') as month, SUM(amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $chartMonths = [];
        for ($m = 1; $m <= 12; $m++) {
            $key = sprintf('%s-%02d', now()->year, $m);
            $chartMonths['values'][] = (float) ($monthly[$key] ?? 0);
            $chartMonths['labels'][] = now()->createFromFormat('Y-m-d', $key.'-01')->format('M');
        }

        $recentPayments = Payment::with('student', 'feeStructure')
            ->where('status', 'recorded')
            ->latest()
            ->limit(6)
            ->get();

        return view('accountant.dashboard', compact(
            'year',
            'term',
            'collected',
            'summary',
            'totalStudents',
            'chartMonths',
            'recentPayments',
        ));
    }
}
