<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Models\Term;

class ReportController extends Controller
{
    public function index()
    {
        $year = AcademicYear::where('is_current', true)->first();
        $term = $year ? Term::where('is_current', true)->where('academic_year_id', $year->id)->first() : null;

        $yearId = $year?->id;
        $termId = $term?->id;

        $revenueQuery = Payment::where('status', 'recorded')
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId));

        $expected = (float) StudentFee::when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->sum('required_amount');

        $outstanding = (float) StudentFee::when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId))
            ->sum('balance');

        $collected = (float) (clone $revenueQuery)->sum('amount');

        $byMethod = (clone $revenueQuery)
            ->selectRaw('method, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('method')
            ->orderByDesc('total')
            ->get();

        $topRows = (clone $revenueQuery)
            ->selectRaw('fee_structure_id, SUM(amount) as total, COUNT(*) as payments')
            ->whereNotNull('fee_structure_id')
            ->groupBy('fee_structure_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        $structures = FeeStructure::whereIn('id', $topRows->pluck('fee_structure_id'))->pluck('name', 'id');

        $topStructures = $topRows->map(fn ($p) => [
            'name' => $structures[$p->fee_structure_id] ?? 'Unknown',
            'total' => (float) $p->total,
            'payments' => (int) $p->payments,
        ])->filter(fn ($row) => $row['name'] !== 'Unknown');

        $methods = Payment::METHODS;

        return view('accountant.reports', compact(
            'year',
            'term',
            'collected',
            'expected',
            'outstanding',
            'byMethod',
            'topStructures',
            'methods',
        ));
    }
}
