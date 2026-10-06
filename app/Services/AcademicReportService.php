<?php

namespace App\Services;

use App\Exports\GenericReportExport;
use App\Exports\MultiSheetExport;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\ReportSequence;
use App\Models\Result;
use App\Models\ResultItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class AcademicReportService
{
    public function __construct(protected GradingService $grading) {}

    /**
     * Whether the current user is allowed to see non-published results.
     */
    public function staffScope(): bool
    {
        return Auth::user()->hasRole('admin', 'teacher', 'accountant');
    }

    /**
     * Apply the status scope for a given report configuration.
     */
    protected function statusScope($query, ?string $status = null)
    {
        if ($this->staffScope()) {
            if ($status && $status !== 'all') {
                return $query->where('status', $status);
            }

            return $query;
        }

        // Students / parents only ever see published results.
        return $query->where('status', Result::STATUS_PUBLISHED);
    }

    /**
     * Unique, non-reused report reference (persisted sequence per year).
     */
    public function reportReference(?int $year = null): string
    {
        return app(ReportReferenceService::class)->next($year);
    }

    /**
     * A report reference used purely for on-screen display purposes.
     */
    public function currentReference(?int $year = null): string
    {
        $year = $year ?? now()->year;
        $seq = ReportSequence::where('year', $year)->first();
        $sequence = $seq ? $seq->sequence + 1 : 1;

        return 'EBSS-RPT-'.$year.'-'.str_pad((string) $sequence, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Report "as of" date.
     */
    public function generatedAt(): string
    {
        return now()->format('d F Y');
    }

    /* ------------------------------------------------------------------ */
    /* Student report card */
    /* ------------------------------------------------------------------ */

    /**
     * @return array{result: ?Result, student: Student, term: Term, year: AcademicYear, attendance: array, reference: string, generated_at: string}
     */
    public function studentReportCard(Student $student, Term $term, AcademicYear $year, ?string $status = null): array
    {
        $result = $student->results()
            ->where('term_id', $term->id)
            ->where('academic_year_id', $year->id)
            ->with(['items.subject', 'schoolClass', 'academicYear'])
            ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
            ->first();

        if ($result && $result->items->isNotEmpty() && $result->items->every(fn ($i) => $i->grade === null)) {
            $result = $this->recomputeItems($result);
        }

        $attendance = $this->attendanceSummary($student, $term, $year);

        return [
            'result' => $result,
            'student' => $student,
            'term' => $term,
            'year' => $year,
            'attendance' => $attendance,
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    /**
     * Ensure stored summary matches live grading rules (single source of truth).
     */
    protected function recomputeItems(Result $result): Result
    {
        foreach ($result->items as $item) {
            if ($item->marks !== null && ($item->grade === null || $item->points === null)) {
                $g = $this->grading->grade((int) $item->marks, $result->level);
                $item->grade = $g['grade'];
                $item->points = $g['points'];
                $item->save();
            }
        }

        $result->update($this->grading->summarize($result->refresh()));

        return $result->refresh();
    }

    public function attendanceSummary(Student $student, Term $term, AcademicYear $year): array
    {
        $records = Attendance::where('student_id', $student->id)
            ->where('term_id', $term->id)
            ->where('academic_year_id', $year->id)
            ->get();

        $days = $records->unique('date')->count();
        $present = $records->where('status', 'present')->count();
        $absent = $records->where('status', 'absent')->count();
        $late = $records->where('status', 'late')->count();

        $percentage = $days > 0 ? round((($present + $late) / $days) * 100, 1) : 0;

        return [
            'days' => $days,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'percentage' => $percentage,
            'has_data' => $days > 0,
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Class results */
    /* ------------------------------------------------------------------ */

    /**
     * @return array{entries: Collection, class: SchoolClass, term: Term, year: AcademicYear, summary: array, reference: string}
     */
    public function classResults(SchoolClass $schoolClass, Term $term, AcademicYear $year, ?string $status = null): array
    {
        $results = $this->resultsForClass($schoolClass, $term, $year, $status)
            ->filter(fn ($r) => $r->items->isNotEmpty());

        if ($results->isNotEmpty()) {
            $this->grading->assignPositions($results);
        }

        $entries = $results->map(function (Result $r) {
            return [
                'result' => $r,
                'student' => $r->student,
            ];
        })->values();

        $summary = $this->classSummary($results, $schoolClass, $term, $year);

        return [
            'entries' => $entries,
            'class' => $schoolClass,
            'term' => $term,
            'year' => $year,
            'summary' => $summary,
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    protected function resultsForClass(SchoolClass $schoolClass, Term $term, AcademicYear $year, ?string $status = null): Collection
    {
        return Result::with(['student', 'schoolClass', 'term', 'items.subject'])
            ->where('school_class_id', $schoolClass->id)
            ->where('term_id', $term->id)
            ->where('academic_year_id', $year->id)
            ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
            ->get();
    }

    protected function classSummary(Collection $results, SchoolClass $schoolClass, Term $term, AcademicYear $year): array
    {
        $averages = $results->pluck('average')->map(fn ($a) => (float) $a);
        $divisions = $results->pluck('division');

        $count = $results->count();
        $classAverage = $averages->isNotEmpty() ? round($averages->avg(), 2) : 0;
        $highest = $averages->isNotEmpty() ? round($averages->max(), 2) : 0;
        $lowest = $averages->isNotEmpty() ? round($averages->min(), 2) : 0;

        $passCount = $results->filter(fn ($r) => $r->failed_subjects === 0 || $r->failed_subjects < $r->passed_subjects)->count();

        return [
            'count' => $count,
            'class_average' => $classAverage,
            'highest_average' => $highest,
            'lowest_average' => $lowest,
            'pass_rate' => $count > 0 ? round(($passCount / $count) * 100, 1) : 0,
            'division_counts' => [
                'Division I' => $this->divisionCount($divisions, 'Division I'),
                'Division II' => $this->divisionCount($divisions, 'Division II'),
                'Division III' => $this->divisionCount($divisions, 'Division III'),
                'Division IV' => $this->divisionCount($divisions, 'Division IV'),
                'Division 0' => $this->divisionCount($divisions, ['Division 0', 'FAIL']),
            ],
        ];
    }

    protected function divisionCount(Collection $divisions, $needle): int
    {
        $needles = is_array($needle) ? $needle : [$needle];

        return $divisions->filter(fn ($d) => in_array($d, $needles, true))->count();
    }

    /* ------------------------------------------------------------------ */
    /* Subject performance */
    /* ------------------------------------------------------------------ */

    /**
     * @return array{rows: Collection, subject: Subject, class: SchoolClass, term: Term, year: AcademicYear, summary: array, reference: string}
     */
    public function subjectPerformance(SchoolClass $schoolClass, Term $term, AcademicYear $year, Subject $subject, ?string $status = null): array
    {
        $results = $this->resultsForClass($schoolClass, $term, $year, $status);

        $rows = $results->map(function (Result $r) use ($subject) {
            $item = $r->items->firstWhere('subject_id', $subject->id);

            return [
                'result' => $r,
                'student' => $r->student,
                'marks' => $item?->marks,
                'grade' => $item?->grade,
                'points' => $item?->points,
                'has_item' => (bool) $item,
            ];
        })->filter(fn ($row) => $row['has_item'])
            ->values();

        $marks = $rows->pluck('marks')->map(fn ($m) => (float) $m);
        $count = $marks->count();

        $passed = $rows->filter(function ($row) {
            if ($row['grade'] === null) {
                return false;
            }

            return $this->grading->grade((int) $row['marks'], $row['result']->level)['is_pass'];
        })->count();

        $summary = [
            'count' => $count,
            'average_mark' => $marks->isNotEmpty() ? round($marks->avg(), 2) : 0,
            'highest_mark' => $marks->isNotEmpty() ? round($marks->max(), 2) : 0,
            'lowest_mark' => $marks->isNotEmpty() ? round($marks->min(), 2) : 0,
            'pass_rate' => $count > 0 ? round(($passed / $count) * 100, 1) : 0,
            'failure_rate' => $count > 0 ? round((($count - $passed) / $count) * 100, 1) : 0,
        ];

        return [
            'rows' => $rows,
            'subject' => $subject,
            'class' => $schoolClass,
            'term' => $term,
            'year' => $year,
            'summary' => $summary,
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    /* ------------------------------------------------------------------ */
    /* Term performance */
    /* ------------------------------------------------------------------ */

    /**
     * @return array{classes: Collection, term: Term, year: AcademicYear, reference: string}
     */
    public function termPerformance(Term $term, AcademicYear $year, ?string $status = null): array
    {
        $results = Result::with('schoolClass')
            ->where('term_id', $term->id)
            ->where('academic_year_id', $year->id)
            ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
            ->get()
            ->filter(fn ($r) => $r->items->isNotEmpty());

        $grouped = $results->groupBy('school_class_id');

        $classes = SchoolClass::query()->orderBy('form_number')->get()
            ->map(function (SchoolClass $class) use ($grouped) {
                $classResults = collect($grouped->get($class->id, []));
                $averages = $classResults->pluck('average')->map(fn ($a) => (float) $a);

                return [
                    'class' => $class,
                    'students' => $classResults->count(),
                    'average' => $averages->isNotEmpty() ? round($averages->avg(), 2) : 0,
                    'highest_average' => $averages->isNotEmpty() ? round($averages->max(), 2) : 0,
                    'lowest_average' => $averages->isNotEmpty() ? round($averages->min(), 2) : 0,
                    'pass_rate' => $this->passRate($classResults),
                    'has_data' => $classResults->isNotEmpty(),
                ];
            })->filter(fn ($row) => $row['has_data'])->values();

        return [
            'classes' => $classes,
            'term' => $term,
            'year' => $year,
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    protected function passRate(Collection $results): float
    {
        $count = $results->count();
        if ($count === 0) {
            return 0;
        }
        $pass = $results->filter(fn ($r) => $r->failed_subjects === 0 || $r->failed_subjects < $r->passed_subjects)->count();

        return round(($pass / $count) * 100, 1);
    }

    /* ------------------------------------------------------------------ */
    /* Academic year comparison */
    /* ------------------------------------------------------------------ */

    /**
     * @return array{terms: Collection, year: AcademicYear, summary: array, reference: string}
     */
    public function academicYearPerformance(AcademicYear $year, ?string $status = null): array
    {
        $allTerms = $year->terms()->orderBy('term_number')->get();

        $terms = $allTerms->map(function (Term $term) use ($year, $status) {
            $results = Result::with('items')
                ->where('academic_year_id', $year->id)
                ->where('term_id', $term->id)
                ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
                ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
                ->get()
                ->filter(fn ($r) => $r->items->isNotEmpty());

            $averages = $results->pluck('average')->map(fn ($a) => (float) $a);

            return [
                'term' => $term,
                'students' => $results->count(),
                'average' => $averages->isNotEmpty() ? round($averages->avg(), 2) : 0,
                'pass_rate' => $this->passRate($results),
                'highest_average' => $averages->isNotEmpty() ? round($averages->max(), 2) : 0,
                'lowest_average' => $averages->isNotEmpty() ? round($averages->min(), 2) : 0,
                'has_data' => $results->isNotEmpty(),
            ];
        })->filter(fn ($row) => $row['has_data']);

        $subjectRows = $this->yearSubjectPerformance($year, $status);

        $summary = [
            'terms' => $terms->count(),
            'term_average' => $terms->isNotEmpty() ? round($terms->avg('average'), 2) : 0,
        ];

        return [
            'terms' => $terms,
            'subject_rows' => $subjectRows,
            'year' => $year,
            'summary' => $summary,
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    protected function yearSubjectPerformance(AcademicYear $year, ?string $status = null): Collection
    {
        $results = Result::with('items.subject')
            ->where('academic_year_id', $year->id)
            ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
            ->get()
            ->filter(fn ($r) => $r->items->isNotEmpty());

        return Subject::withCount(['resultItems'])
            ->orderBy('name')
            ->get()
            ->map(function (Subject $subject) use ($results) {
                $marks = collect();
                foreach ($results as $result) {
                    $item = $result->items->firstWhere('subject_id', $subject->id);
                    if ($item && $item->marks !== null) {
                        $marks->push((float) $item->marks);
                    }
                }

                return [
                    'subject' => $subject,
                    'count' => $marks->count(),
                    'average_mark' => $marks->isNotEmpty() ? round($marks->avg(), 2) : 0,
                    'has_data' => $marks->isNotEmpty(),
                ];
            })->filter(fn ($row) => $row['has_data'])->values();
    }

    /* ------------------------------------------------------------------ */
    /* Whole school performance */
    /* ------------------------------------------------------------------ */

    /**
     * @return array{overview: array, classes: Collection, best: array, reference: string}
     */
    public function schoolPerformance(AcademicYear $year, ?Term $term = null, ?string $status = null): array
    {
        $results = Result::with(['student', 'schoolClass', 'items.subject'])
            ->where('academic_year_id', $year->id)
            ->when($term, fn ($q) => $q->where('term_id', $term->id))
            ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
            ->get()
            ->filter(fn ($r) => $r->items->isNotEmpty());

        $totalStudents = $results->unique('student_id')->count();
        $totalSubjects = $results->flatMap(fn ($r) => $r->items)->unique('subject_id')->count();

        $averages = $results->pluck('average')->map(fn ($a) => (float) $a);
        $overallAverage = $averages->isNotEmpty() ? round($averages->avg(), 2) : 0;
        $overallPassRate = $this->passRate($results);

        $classes = [];
        foreach ($this->resultsForSchool($results) as $classId => $classResults) {
            $class = $classResults->first()?->schoolClass;
            if (! $class) {
                continue;
            }
            $cAvg = $classResults->pluck('average')->map(fn ($a) => (float) $a);
            $classes[] = [
                'class' => $class,
                'students' => $classResults->count(),
                'average' => $cAvg->isNotEmpty() ? round($cAvg->avg(), 2) : 0,
                'pass_rate' => $this->passRate($classResults),
            ];
        }
        $classes = collect($classes)->sortByDesc('average')->values();

        $bestClass = $classes->first();
        $bestResult = $results->sortByDesc('average')->first();
        $bestSubject = $this->bestSubject($results);

        return [
            'overview' => [
                'total_students' => $totalStudents,
                'total_subjects' => $totalSubjects,
                'overall_average' => $overallAverage,
                'overall_pass_rate' => $overallPassRate,
            ],
            'best' => [
                'class' => $bestClass,
                'student' => $bestResult,
                'subject' => $bestSubject,
            ],
            'classes' => $classes,
            'term' => $term,
            'year' => $year,
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    protected function resultsForSchool(Collection $results): Collection
    {
        return $results->groupBy('school_class_id');
    }

    protected function bestSubject(Collection $results): ?array
    {
        $agg = [];
        foreach ($results as $result) {
            foreach ($result->items as $item) {
                if ($item->marks === null) {
                    continue;
                }
                $agg[$item->subject_id]['marks'][] = (float) $item->marks;
                $agg[$item->subject_id]['subject'] = $item->subject;
            }
        }

        $best = null;
        foreach ($agg as $entry) {
            $avg = round(collect($entry['marks'])->avg(), 2);
            if (! $best || $avg > $best['average']) {
                $best = ['subject' => $entry['subject'], 'average' => $avg];
            }
        }

        return $best;
    }

    /* ------------------------------------------------------------------ */
    /* Level performance (O-Level / A-Level) */
    /* ------------------------------------------------------------------ */

    public function levelPerformance(string $level, Term $term, AcademicYear $year, ?string $status = null): array
    {
        $classes = SchoolClass::where('level', $level)->orderBy('form_number')->pluck('id');
        $results = Result::with(['student', 'schoolClass', 'items.subject'])
            ->whereIn('school_class_id', $classes)
            ->where('term_id', $term->id)
            ->where('academic_year_id', $year->id)
            ->when($this->staffScope() && $status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->when(! $this->staffScope(), fn ($q) => $q->where('status', Result::STATUS_PUBLISHED))
            ->get()
            ->filter(fn ($r) => $r->items->isNotEmpty());

        $classPerformance = SchoolClass::where('level', $level)->orderBy('form_number')->get()
            ->map(function (SchoolClass $class) use ($results) {
                $classResults = $results->where('school_class_id', $class->id);
                $averages = $classResults->pluck('average')->map(fn ($a) => (float) $a);

                return [
                    'class' => $class,
                    'students' => $classResults->count(),
                    'average' => $averages->isNotEmpty() ? round($averages->avg(), 2) : 0,
                    'pass_rate' => $this->passRate(collect($classResults->values())),
                    'has_data' => $classResults->isNotEmpty(),
                ];
            })->filter(fn ($row) => $row['has_data']);

        $subjectPerformance = Subject::where('level', $level)->orderBy('name')->get()
            ->map(function (Subject $subject) use ($results) {
                $marks = collect();
                foreach ($results as $result) {
                    $item = $result->items->firstWhere('subject_id', $subject->id);
                    if ($item && $item->marks !== null) {
                        $marks->push((float) $item->marks);
                    }
                }
                $passed = $marks->filter(fn ($m) => $this->grading->grade((int) $m, $subject->level)['is_pass'])->count();

                return [
                    'subject' => $subject,
                    'count' => $marks->count(),
                    'average_mark' => $marks->isNotEmpty() ? round($marks->avg(), 2) : 0,
                    'pass_rate' => $marks->count() > 0 ? round(($passed / $marks->count()) * 100, 1) : 0,
                    'has_data' => $marks->isNotEmpty(),
                ];
            })->filter(fn ($row) => $row['has_data']);

        $divisionDistribution = $results->groupBy(fn ($r) => $this->normalizeDivision($r->division))
            ->map(fn ($group) => $group->count());

        $averages = $results->pluck('average')->map(fn ($a) => (float) $a);

        return [
            'level' => $level,
            'term' => $term,
            'year' => $year,
            'classes' => $classPerformance,
            'subjects' => $subjectPerformance,
            'division_distribution' => $divisionDistribution,
            'overall_average' => $averages->isNotEmpty() ? round($averages->avg(), 2) : 0,
            'pass_rate' => $this->passRate(collect($results->values())),
            'total_students' => $results->unique('student_id')->count(),
            'reference' => $this->reportReference(),
            'generated_at' => $this->generatedAt(),
        ];
    }

    protected function normalizeDivision(?string $division): string
    {
        if (! $division) {
            return 'Division 0';
        }
        if (str_contains(strtolower($division), 'fail')) {
            return 'Division 0';
        }

        return $division;
    }

    /* ------------------------------------------------------------------ */
    /* CSV export */
    /* ------------------------------------------------------------------ */

    public function classCsv(SchoolClass $schoolClass, Term $term, AcademicYear $year, ?string $status = null): string
    {
        $data = $this->classResults($schoolClass, $term, $year, $status);

        $lines = [];
        $lines[] = ['Position', 'Admission Number', 'Student Name', 'Total', 'Average', 'Division', 'Status'];

        foreach ($data['entries'] as $entry) {
            $r = $entry['result'];
            $s = $entry['student'];
            $lines[] = [
                trim((string) $r->position),
                $s->admission_number,
                $s->full_name,
                (string) $r->total_marks,
                number_format((float) $r->average, 2),
                (string) $r->division,
                $r->failed_subjects === 0 || $r->failed_subjects < $r->passed_subjects ? 'PASS' : 'FAIL',
            ];
        }

        return $this->toCsv($lines);
    }

    public function subjectCsv(SchoolClass $schoolClass, Term $term, AcademicYear $year, Subject $subject, ?string $status = null): string
    {
        $data = $this->subjectPerformance($schoolClass, $term, $year, $subject, $status);

        $lines = [];
        $lines[] = ['Position', 'Student', 'Admission Number', 'Marks', 'Grade', 'Points', 'Status'];

        $rows = $data['rows']->values();
        $marks = $data['rows']->pluck('marks')->map(fn ($m) => (float) $m)->sortDesc()->values();
        $rankMap = collect();
        $marks->values()->each(function ($m, $i) use (&$rankMap) {
            if (! $rankMap->has((string) $m)) {
                $rankMap[(string) $m] = $i + 1;
            }
        });

        foreach ($rows as $i => $row) {
            $lines[] = [
                (string) ($rankMap[(string) $row['marks']] ?? $i + 1),
                $row['student']->full_name,
                $row['student']->admission_number,
                (string) $row['marks'],
                (string) $row['grade'],
                (string) $row['points'],
                $row['grade'] !== 'F' ? 'PASS' : 'FAIL',
            ];
        }

        return $this->toCsv($lines);
    }

    protected function toCsv(array $lines): string
    {
        $out = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel
        foreach ($lines as $line) {
            $out .= implode(',', array_map(function ($field) {
                $field = (string) $field;

                return '"'.str_replace('"', '""', $field).'"';
            }, $line))."\r\n";
        }

        return $out;
    }

    /* ------------------------------------------------------------------ */
    /* Filter option helpers */
    /* ------------------------------------------------------------------ */

    public function classesForLevel(?string $level)
    {
        return SchoolClass::query()
            ->when($level && $level !== 'all', fn ($q) => $q->where('level', $level))
            ->orderBy('form_number')
            ->get();
    }

    public function subjectsForLevel(?string $level)
    {
        return Subject::query()
            ->when($level && $level !== 'all', fn ($q) => $q->where('level', $level))
            ->orderBy('name')
            ->get();
    }

    public function studentsForClass(?int $classId)
    {
        return Student::query()
            ->when($classId, fn ($q) => $q->where('school_class_id', $classId))
            ->orderBy('first_name')
            ->get();
    }

    /* ------------------------------------------------------------------ */
    /* Excel exports */
    /* ------------------------------------------------------------------ */

    public function excelStudentReport(Student $student, Term $term, AcademicYear $year, ?string $status = null): MultiSheetExport
    {
        $data = $this->studentReportCard($student, $term, $year, $status);

        $rows = collect($data['result']?->items ?? [])->map(function (ResultItem $item, $index) {
            return [
                str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                $item->subject?->name ?? '—',
                $item->marks ?? '—',
                $item->grade ?? '—',
                $item->points ?? '—',
                $this->itemStatus($item),
            ];
        })->values();

        $sheets = [
            new GenericReportExport(
                ['No.', 'Subject', 'Marks', 'Grade', 'Points', 'Status'],
                $rows,
                'Subject Results'
            ),
        ];

        if ($data['result']) {
            $summary = [
                ['Summary'],
                ['Total Marks', $data['result']->total_marks],
                ['Average', number_format((float) $data['result']->average, 2).'%'],
                ['Position', $data['result']->position ?? '—'],
                ['Division', $data['result']->division ?? '—'],
                ['Passed Subjects', $data['result']->passed_subjects],
                ['Failed Subjects', $data['result']->failed_subjects],
                ['Subjects Taken', $data['result']->items->count()],
                ['Overall Status', $data['result']->failed_subjects === 0 || $data['result']->failed_subjects < $data['result']->passed_subjects ? 'PASS' : 'FAIL'],
            ];
            $sheets[] = new GenericReportExport(
                ['Metric', 'Value'],
                $summary,
                'Summary'
            );
        }

        if (! empty($data['attendance']['has_data'])) {
            $sheets[] = new GenericReportExport(
                ['Metric', 'Value'],
                [
                    ['School Days', $data['attendance']['days']],
                    ['Present', $data['attendance']['present']],
                    ['Absent', $data['attendance']['absent']],
                    ['Late', $data['attendance']['late']],
                    ['Attendance %', $data['attendance']['percentage']], ],
                'Attendance'
            );
        }

        return new MultiSheetExport($sheets);
    }

    public function excelClassReport(SchoolClass $schoolClass, Term $term, AcademicYear $year, ?string $status = null): MultiSheetExport
    {
        $data = $this->classResults($schoolClass, $term, $year, $status);

        $rows = collect($data['entries'])->map(function ($entry) {
            $r = $entry['result'];
            $s = $entry['student'];

            return [
                trim((string) $r->position),
                $s->admission_number,
                $s->full_name,
                (string) $r->total_marks,
                number_format((float) $r->average, 2),
                (string) $r->division,
                $this->passFail($r),
            ];
        })->values();

        $summary = $data['summary'];
        $sheets = [
            new GenericReportExport(
                ['Position', 'Admission No.', 'Student Name', 'Total Marks', 'Average', 'Division', 'Status'],
                $rows,
                'Class Results'
            ),
            new GenericReportExport(
                ['Metric', 'Value'],
                [
                    ['Class / Form', $schoolClass->name],
                    ['Level', $schoolClass->level],
                    ['Academic Year', $year->name],
                    ['Term', $term->name],
                    ['Students', $summary['count']],
                    ['Class Average', $summary['class_average'].'%'],
                    ['Highest Average', $summary['highest_average'].'%'],
                    ['Lowest Average', $summary['lowest_average'].'%'],
                    ['Pass Rate', $summary['pass_rate'].'%'],
                    ['Division I', $summary['division_counts']['Division I']],
                    ['Division II', $summary['division_counts']['Division II']],
                    ['Division III', $summary['division_counts']['Division III']],
                    ['Division IV', $summary['division_counts']['Division IV']],
                    ['Division 0', $summary['division_counts']['Division 0']], ],
                'Summary'
            ),
        ];

        return new MultiSheetExport($sheets);
    }

    public function excelSubjectReport(SchoolClass $schoolClass, Term $term, AcademicYear $year, Subject $subject, ?string $status = null): MultiSheetExport
    {
        $data = $this->subjectPerformance($schoolClass, $term, $year, $subject, $status);

        $rows = collect($data['rows']->values());
        $marks = $rows->pluck('marks')->map(fn ($m) => (float) $m)->sortDesc()->values();
        $rankMap = collect();
        $marks->values()->each(function ($m, $i) use (&$rankMap) {
            if (! $rankMap->has((string) $m)) {
                $rankMap[(string) $m] = $i + 1;
            }
        });

        $exported = $rows->map(function ($row, $i) use ($rankMap) {
            return [
                (string) ($rankMap[(string) $row['marks']] ?? $i + 1),
                $row['student']->admission_number,
                $row['student']->full_name,
                (string) $row['marks'],
                (string) $row['grade'],
                (string) $row['points'],
                $row['grade'] !== 'F' ? 'PASS' : 'FAIL',
            ];
        });

        $summary = $data['summary'];

        return new MultiSheetExport([
            new GenericReportExport(
                ['Position', 'Admission No.', 'Student Name', 'Marks', 'Grade', 'Points', 'Status'],
                $exported,
                'Subject Results'
            ),
            new GenericReportExport(
                ['Metric', 'Value'],
                [
                    ['Subject', $subject->name.' ('.$subject->code.')'],
                    ['Class / Form', $schoolClass->name],
                    ['Term', $term->name],
                    ['Year', $year->name],
                    ['Students', $summary['count']],
                    ['Average Mark', $summary['average_mark']],
                    ['Highest Mark', $summary['highest_mark']],
                    ['Lowest Mark', $summary['lowest_mark']],
                    ['Pass Rate', $summary['pass_rate'].'%'],
                    ['Failure Rate', $summary['failure_rate'].'%'], ],
                'Summary'
            ),
        ]);
    }

    public function excelTermReport(Term $term, AcademicYear $year, ?string $status = null): MultiSheetExport
    {
        $data = $this->termPerformance($term, $year, $status);

        $rows = collect($data['classes'])->map(function ($row) {
            return [
                $row['class']->name,
                $row['class']->level,
                (string) $row['students'],
                number_format((float) $row['average'], 2),
                number_format((float) $row['highest_average'], 2),
                number_format((float) $row['lowest_average'], 2),
                $row['pass_rate'].'%',
            ];
        })->values();

        return new MultiSheetExport([
            new GenericReportExport(
                ['Class / Form', 'Level', 'Students', 'Average', 'Highest Avg', 'Lowest Avg', 'Pass Rate'],
                $rows,
                'Classes'
            ),
        ]);
    }

    public function excelYearReport(AcademicYear $year, ?string $status = null): MultiSheetExport
    {
        $data = $this->academicYearPerformance($year, $status);

        $termRows = collect($data['terms'])->map(function ($row) {
            return [
                $row['term']->name,
                (string) $row['students'],
                number_format((float) $row['average'], 2),
                $row['pass_rate'].'%',
                number_format((float) $row['highest_average'], 2),
                number_format((float) $row['lowest_average'], 2),
            ];
        })->values();

        $subjectRows = collect($data['subject_rows'])->map(function ($row) {
            return [
                $row['subject']->name.' ('.$row['subject']->code.')',
                (string) $row['count'],
                number_format((float) $row['average_mark'], 2),
            ];
        })->values();

        return new MultiSheetExport([
            new GenericReportExport(
                ['Term', 'Students', 'Average', 'Pass Rate', 'Highest Avg', 'Lowest Avg'],
                $termRows,
                'Term Comparison'
            ),
            new GenericReportExport(
                ['Subject', 'Students', 'Average Mark'],
                $subjectRows,
                'Subject Performance'
            ),
        ]);
    }

    public function excelSchoolReport(AcademicYear $year, ?Term $term = null, ?string $status = null): MultiSheetExport
    {
        $data = $this->schoolPerformance($year, $term, $status);

        $classRows = collect($data['classes'])->map(function ($row) {
            return [
                $row['class']->name,
                $row['class']->level,
                (string) $row['students'],
                number_format((float) $row['average'], 2),
                $row['pass_rate'].'%',
            ];
        })->values();

        return new MultiSheetExport([
            new GenericReportExport(
                ['Metric', 'Value'],
                [
                    ['Academic Year', $year->name],
                    ['Term', $term?->name ?? 'Full Year'],
                    ['Total Students', $data['overview']['total_students']],
                    ['Total Subjects', $data['overview']['total_subjects']],
                    ['Overall Average', $data['overview']['overall_average'].'%'],
                    ['Overall Pass Rate', $data['overview']['overall_pass_rate'].'%'],
                    ['Best Class', $data['best']['class']['class']->name ?? '—'],
                    ['Best Class Average', isset($data['best']['class']) ? number_format((float) $data['best']['class']['average'], 2) : '—'],
                    ['Best Subject', $data['best']['subject']['subject']->name ?? '—'],
                    ['Best Subject Average', isset($data['best']['subject']) ? number_format((float) $data['best']['subject']['average'], 2) : '—'],
                ],
                'Overview'
            ),
            new GenericReportExport(
                ['Class / Form', 'Level', 'Students', 'Average', 'Pass Rate'],
                $classRows,
                'Classes'
            ),
        ]);
    }

    public function excelLevelReport(string $level, Term $term, AcademicYear $year, ?string $status = null): MultiSheetExport
    {
        $data = $this->levelPerformance($level, $term, $year, $status);

        $classRows = collect($data['classes'])->map(function ($row) {
            return [
                $row['class']->name,
                (string) $row['students'],
                number_format((float) $row['average'], 2),
                $row['pass_rate'].'%',
            ];
        })->values();

        $subjectRows = collect($data['subjects'])->map(function ($row) {
            return [
                $row['subject']->name.' ('.$row['subject']->code.')',
                (string) $row['count'],
                number_format((float) $row['average_mark'], 2),
                $row['pass_rate'].'%',
            ];
        })->values();

        return new MultiSheetExport([
            new GenericReportExport(
                ['Class / Form', 'Students', 'Average', 'Pass Rate'],
                $classRows,
                'Classes'
            ),
            new GenericReportExport(
                ['Subject', 'Students', 'Average Mark', 'Pass Rate'],
                $subjectRows,
                'Subject Performance'
            ),
            new GenericReportExport(
                ['Division', 'Students'],
                collect($data['division_distribution'])->map(fn ($count, $division) => [$division, (string) $count])->values(),
                'Division Distribution'
            ),
        ]);
    }

    protected function passFail(Result $result): string
    {
        return $result->failed_subjects === 0 || $result->failed_subjects < $result->passed_subjects ? 'PASS' : 'FAIL';
    }

    protected function itemStatus(ResultItem $item): string
    {
        return $item->grade === 'F' ? 'FAIL' : 'PASS';
    }
}
