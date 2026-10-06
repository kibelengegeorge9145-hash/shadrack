<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\ResultItem;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Term;
use App\Services\AuditLogService;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        abort_if(! $teacher, 403, 'No teacher profile is linked to your account.');

        $classes = $teacher->classes()->orderBy('form_number')->get();
        $terms = Term::with('academicYear')->orderBy('academic_year_id')->orderBy('term_number')->get();
        $termOptions = $terms->mapWithKeys(fn ($term) => [
            $term->id => $term->name.($term->academicYear ? ' · '.$term->academicYear->name : ''),
        ]);

        $selectedClass = null;
        $selectedSubject = null;
        $selectedTerm = null;
        $subjects = collect();
        $students = collect();
        $marks = [];

        if ($request->filled('school_class_id')) {
            $selectedClass = $classes->firstWhere('id', (int) $request->school_class_id);
            abort_if(! $selectedClass, 403);

            $subjects = $teacher->subjects()
                ->wherePivot('school_class_id', $selectedClass->id)
                ->orderBy('name')
                ->get();
        }

        if ($selectedClass && $request->filled('subject_id') && $request->filled('term_id')) {
            $selectedSubject = $subjects->firstWhere('id', (int) $request->subject_id);
            $selectedTerm = $terms->firstWhere('id', (int) $request->term_id);
            abort_if(! $selectedSubject || ! $selectedTerm, 403);

            $students = $selectedClass->students()
                ->active()
                ->orderBy('first_name')
                ->get();

            $items = ResultItem::with('result')
                ->where('subject_id', $selectedSubject->id)
                ->whereHas(
                    'result',
                    fn ($q) => $q->where('school_class_id', $selectedClass->id)
                        ->where('term_id', $selectedTerm->id)
                )
                ->get();

            foreach ($items as $item) {
                if ($item->result) {
                    $marks[$item->result->student_id] = $item->marks;
                }
            }
        }

        return view('teacher.results', compact(
            'teacher',
            'classes',
            'selectedClass',
            'subjects',
            'termOptions',
            'selectedSubject',
            'selectedTerm',
            'students',
            'marks',
        ));
    }

    public function store(Request $request, GradingService $grading)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['required', 'exists:subjects,id'],
            'term_id' => ['required', 'exists:terms,id'],
            'marks' => ['nullable', 'array'],
            'marks.*' => ['nullable', 'integer', 'between:0,100'],
        ]);

        $teacher = Auth::user()->teacher;
        abort_if(! $teacher, 403, 'No teacher profile is linked to your account.');

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $subject = Subject::findOrFail($data['subject_id']);
        $term = Term::findOrFail($data['term_id']);

        abort_if(! $teacher->classes()->whereKey($class->id)->exists(), 403, 'You are not assigned to this class.');
        abort_if(
            ! $teacher->subjects()->wherePivot('school_class_id', $class->id)->whereKey($subject->id)->exists(),
            403,
            'You do not teach this subject in the selected class.'
        );

        $yearId = $term->academic_year_id;
        $marks = $data['marks'] ?? [];

        $saved = 0;
        foreach ($marks as $studentId => $mark) {
            $mark = trim((string) $mark);
            $student = $class->students()->whereKey($studentId)->first();
            if (! $student) {
                continue;
            }

            $result = Result::where('student_id', $studentId)
                ->where('academic_year_id', $yearId)
                ->where('term_id', $term->id)
                ->where('school_class_id', $class->id)
                ->first();

            if ($mark === '') {
                if ($result) {
                    ResultItem::where('result_id', $result->id)->where('subject_id', $subject->id)->delete();
                }

                continue;
            }

            $result = $result ?? Result::create([
                'student_id' => $studentId,
                'school_class_id' => $class->id,
                'academic_year_id' => $yearId,
                'term_id' => $term->id,
                'level' => $class->level,
                'status' => Result::STATUS_SUBMITTED,
                'submitted_by' => Auth::id(),
            ]);

            $graded = $grading->grade((int) $mark, $class->level);

            ResultItem::updateOrCreate(
                ['result_id' => $result->id, 'subject_id' => $subject->id],
                [
                    'marks' => (int) $mark,
                    'grade' => $graded['grade'],
                    'points' => $graded['points'],
                    'teacher_id' => $teacher->id,
                ]
            );

            $result->load('items');
            $summary = $grading->summarize($result);
            $result->fill($summary + [
                'status' => Result::STATUS_SUBMITTED,
                'submitted_by' => Auth::id(),
            ])->save();

            $saved++;
        }

        $classResults = Result::where('school_class_id', $class->id)
            ->where('term_id', $term->id)
            ->where('academic_year_id', $yearId)
            ->with('items')
            ->get()
            ->filter(fn ($r) => $r->items->isNotEmpty());

        if ($classResults->isNotEmpty()) {
            $grading->assignPositions($classResults);
        }

        AuditLogService::log(
            'results.saved',
            "Saved results for {$class->name} - {$subject->name} - {$term->name} ({$saved} student(s))",
            $class,
            [
                'subject_id' => $subject->id,
                'term_id' => $term->id,
                'marks_saved' => $saved,
            ],
        );

        return back()->with('success', "Marks saved for {$saved} student(s). Positions have been recalculated.");
    }
}
