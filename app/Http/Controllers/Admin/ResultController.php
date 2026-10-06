<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use App\Services\GradingService;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('form_number')->get();
        $terms = Term::with('academicYear')->orderBy('academic_year_id')->orderBy('term_number')->get();
        $termOptions = $terms->mapWithKeys(fn ($term) => [
            $term->id => $term->name.($term->academicYear ? ' · '.$term->academicYear->name : ''),
        ]);
        $statusOptions = collect(Result::STATUSES)->mapWithKeys(fn ($status) => [$status => ucfirst($status)]);

        $results = Result::with(['student', 'schoolClass', 'term'])
            ->when($request->filled('term_id'), fn ($q) => $q->where('term_id', $request->term_id))
            ->when($request->filled('school_class_id'), fn ($q) => $q->where('school_class_id', $request->school_class_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.results.index', compact('classes', 'termOptions', 'statusOptions', 'results'));
    }

    public function reportCard(Student $student, Term $term, GradingService $grading)
    {
        $result = $student->results()
            ->where('term_id', $term->id)
            ->with(['items.subject', 'schoolClass', 'academicYear'])
            ->first();

        if ($result && ! $result->position) {
            $classResults = Result::where('school_class_id', $result->school_class_id)
                ->where('term_id', $term->id)
                ->where('academic_year_id', $result->academic_year_id)
                ->with('items')
                ->get()
                ->filter(fn ($r) => $r->items->isNotEmpty());

            if ($classResults->isNotEmpty()) {
                $grading->assignPositions($classResults);
                $result->refresh();
            }
        }

        return view('admin.results.report-card', compact('student', 'term', 'result'));
    }
}
