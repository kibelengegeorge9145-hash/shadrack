<?php

namespace App\Http\Controllers\Accountant;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\StudentFee;
use App\Models\Term;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index(Request $request)
    {
        $year = AcademicYear::where('is_current', true)->first();
        $term = $year ? Term::where('is_current', true)->where('academic_year_id', $year->id)->first() : null;

        $yearId = $year?->id;
        $termId = $term?->id;

        $structures = FeeStructure::with('schoolClass', 'academicYear')
            ->where('is_active', true)
            ->when($yearId, fn ($q) => $q->where(fn ($s) => $s->whereNull('academic_year_id')->orWhere('academic_year_id', $yearId)))
            ->latest()
            ->get();

        $ledgerQuery = StudentFee::with('student', 'student.schoolClass', 'feeStructure', 'academicYear', 'term')
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->when($termId, fn ($q) => $q->where('term_id', $termId));

        if ($studentSearch = $request->get('student')) {
            $ledgerQuery->whereHas('student', function ($q) use ($studentSearch) {
                $q->where('first_name', 'like', "%{$studentSearch}%")
                    ->orWhere('middle_name', 'like', "%{$studentSearch}%")
                    ->orWhere('last_name', 'like', "%{$studentSearch}%")
                    ->orWhere('admission_number', 'like', "%{$studentSearch}%");
            });
        }

        if ($classId = $request->get('class')) {
            $ledgerQuery->whereHas('student', fn ($q) => $q->where('school_class_id', $classId));
        }

        if ($status = $request->get('status')) {
            if ($status === 'unpaid') {
                $ledgerQuery->where('paid_amount', 0);
            } elseif ($status === 'partial') {
                $ledgerQuery->where('balance', '>', 0)->where('paid_amount', '>', 0);
            } elseif ($status === 'paid') {
                $ledgerQuery->where('balance', 0)->where('paid_amount', '>', 0);
            }
        }

        $ledger = $ledgerQuery->latest()->paginate(15)->withQueryString();

        $classes = SchoolClass::orderBy('name')->pluck('name', 'id');

        return view('accountant.fees', compact('structures', 'ledger', 'year', 'term', 'classes'));
    }
}
