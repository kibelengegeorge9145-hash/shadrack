<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicReportRequest;
use App\Models\AcademicYear;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Services\AcademicReportService;
use App\Services\AuditLogService;
use App\Services\ReportPdfService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;

class AcademicReportController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected AcademicReportService $service) {}

    /**
     * Route prefix for the staff report center (teacher vs admin).
     */
    protected function staffPrefix(): string
    {
        return request()->user()?->isTeacher() ? 'teacher' : 'admin';
    }

    /**
     * Build a staff report-center route name, e.g. admin.reports.academic.pdf.
     */
    protected function reportRoute(string $name, array $params = []): string
    {
        return route($this->staffPrefix().'.reports.academic'.($name ? '.'.$name : ''), $params);
    }

    /**
     * Academic Report Center (filter + launchpad).
     */
    public function index()
    {
        $this->authorize('viewAny', Result::class);
        $this->authorize('generate-academic-reports');

        $years = AcademicYear::orderByDesc('start_date')->get();
        $currentYear = AcademicYear::where('is_current', true)->first();
        $classes = SchoolClass::orderBy('form_number')->get();
        $subjects = Subject::orderBy('name')->get();
        $students = Student::with('schoolClass')->orderBy('first_name')->get();

        return view('admin.reports.academic-center', compact(
            'years',
            'currentYear',
            'classes',
            'subjects',
            'students'
        ) + ['staffPrefix' => $this->staffPrefix()]);
    }

    /**
     * JSON list of terms for a given academic year.
     */
    public function terms(Request $request)
    {
        $this->authorize('viewAny', Result::class);

        $yearId = $request->integer('academic_year_id', $request->integer('year'));
        $terms = $yearId
            ? AcademicYear::findOrFail($yearId)->terms()->orderBy('term_number')->get()
            : Term::query()->orderBy('term_number')->get();

        return response()->json([
            'terms' => $terms->map(fn (Term $term) => [
                'id' => $term->id,
                'name' => $term->name,
            ])->values(),
            'default_term' => $terms->first()?->id,
        ]);
    }

    /**
     * Generate / preview a report based on selected filters.
     */
    public function preview(AcademicReportRequest $request)
    {
        $this->authorize('viewAny', Result::class);
        $this->authorize('generate-academic-reports');

        $filters = $this->normalize($request);
        $type = $filters['report_type'];

        $year = $filters['year']
            ? AcademicYear::findOrFail($filters['year'])
            : AcademicYear::where('is_current', true)->first();

        if (! $year) {
            abort(422, 'No academic year available.');
        }

        $term = $filters['term']
            ? Term::findOrFail($filters['term'])
            : $year->terms()->orderBy('term_number')->first();

        if (in_array($type, [
            AcademicReportRequest::TYPE_STUDENT_CARD,
            AcademicReportRequest::TYPE_CLASS,
            AcademicReportRequest::TYPE_SUBJECT,
            AcademicReportRequest::TYPE_TERM,
            AcademicReportRequest::TYPE_OLEVEL,
            AcademicReportRequest::TYPE_ALEVEL,
        ], true) && ! $term) {
            abort(422, 'A term is required for this report type.');
        }

        $report = $this->buildReport($type, $filters, $year, $term);
        $this->audit($type, $report['audit'] ?? []);

        return view($report['view'], array_merge($report['data'], [
            'report_type' => $type,
            'report_type_label' => AcademicReportRequest::TYPES[$type],
            'staffPrefix' => $this->staffPrefix(),
            'pdfUrl' => $this->reportRoute('pdf', $request->query()),
            'excelUrl' => $this->reportRoute('excel', $request->query()),
        ]));
    }

    /**
     * Export class / subject reports to CSV (Excel-compatible).
     */
    public function exportCsv(AcademicReportRequest $request): StreamedResponse
    {
        $this->authorize('viewAny', Result::class);
        $this->authorize('generate-academic-reports');

        $filters = $this->normalize($request);
        $type = $filters['report_type'];

        if (! in_array($type, [
            AcademicReportRequest::TYPE_CLASS,
            AcademicReportRequest::TYPE_SUBJECT,
        ], true)) {
            abort(422, 'CSV export is only available for class and subject reports.');
        }

        $year = $filters['year']
            ? AcademicYear::findOrFail($filters['year'])
            : AcademicYear::where('is_current', true)->first();

        if (! $year) {
            abort(422, 'No academic year available.');
        }

        $term = $filters['term']
            ? Term::findOrFail($filters['term'])
            : $year->terms()->orderBy('term_number')->first();

        if (! $term) {
            abort(422, 'A term is required for CSV export.');
        }

        $schoolClass = $filters['class']
            ? SchoolClass::findOrFail($filters['class'])
            : null;

        if (! $schoolClass) {
            abort(422, 'A class is required for CSV export.');
        }

        $status = $filters['status'];

        if ($type === AcademicReportRequest::TYPE_CLASS) {
            $this->authorize('viewClass', $schoolClass);

            $csv = $this->service->classCsv($schoolClass, $term, $year, $status);
            $filename = 'class-report-'.$schoolClass->id.'-'.$term->id.'-'.$year->id.'.csv';
        } else {
            $subject = $filters['subject']
                ? Subject::findOrFail($filters['subject'])
                : null;

            if (! $subject) {
                abort(422, 'A subject is required for subject CSV export.');
            }

            $this->authorize('viewSubject', [$subject, $schoolClass]);

            $csv = $this->service->subjectCsv($schoolClass, $term, $year, $subject, $status);
            $filename = 'subject-report-'.$subject->id.'-'.$schoolClass->id.'-'.$term->id.'-'.$year->id.'.csv';
        }

        $this->audit($type.'_csv', [
            'year_id' => $year->id,
            'term_id' => $term->id,
            'class_id' => $schoolClass->id,
            'subject_id' => $filters['subject'],
            'status' => $status,
        ]);

        return response()->streamDownload(
            static function () use ($csv): void {
                echo $csv;
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            ]
        );
    }

    /**
     * Download a PDF of the selected report (staff, all types).
     */
    public function pdf(AcademicReportRequest $request)
    {
        $this->authorize('viewAny', Result::class);
        $this->authorize('generate-academic-reports');

        $filters = $this->normalize($request);
        $type = $filters['report_type'];

        $year = $filters['year']
            ? AcademicYear::findOrFail($filters['year'])
            : AcademicYear::where('is_current', true)->first();

        if (! $year) {
            abort(422, 'No academic year available.');
        }

        $term = $filters['term']
            ? Term::findOrFail($filters['term'])
            : $year->terms()->orderBy('term_number')->first();

        if (in_array($type, [
            AcademicReportRequest::TYPE_STUDENT_CARD,
            AcademicReportRequest::TYPE_CLASS,
            AcademicReportRequest::TYPE_SUBJECT,
            AcademicReportRequest::TYPE_TERM,
            AcademicReportRequest::TYPE_OLEVEL,
            AcademicReportRequest::TYPE_ALEVEL,
        ], true) && ! $term) {
            abort(422, 'A term is required for this report type.');
        }

        $report = $this->buildReport($type, $filters, $year, $term);

        $pdf = app(ReportPdfService::class)->generate($report['view'], $report['data']);
        $filename = $this->assetFilename('pdf', $type, $report['data']);

        $this->audit($type.'_pdf', ($report['audit'] ?? []) + ['filename' => $filename]);

        $headers = app(ReportPdfService::class)->headers($filename);

        return response($pdf, 200, $headers);
    }

    /**
     * Download an Excel (.xlsx) workbook of the selected report (staff, all types).
     */
    public function exportExcel(AcademicReportRequest $request)
    {
        $this->authorize('viewAny', Result::class);
        $this->authorize('generate-academic-reports');

        $filters = $this->normalize($request);
        $type = $filters['report_type'];
        $year = $this->resolveYear($filters);
        $term = $this->resolveTerm($filters, $year);

        if (in_array($type, [
            AcademicReportRequest::TYPE_STUDENT_CARD,
            AcademicReportRequest::TYPE_CLASS,
            AcademicReportRequest::TYPE_SUBJECT,
            AcademicReportRequest::TYPE_TERM,
            AcademicReportRequest::TYPE_OLEVEL,
            AcademicReportRequest::TYPE_ALEVEL,
        ], true) && ! $term) {
            abort(422, 'A term is required for this report type.');
        }

        $status = $filters['status'];
        $audit = [
            'year_id' => $year->id,
            'term_id' => $term?->id,
            'level' => $filters['level'],
            'status' => $status,
        ];

        $export = match ($type) {
            AcademicReportRequest::TYPE_STUDENT_CARD => $this->excelStudent($filters, $year, $term, $status, $audit),
            AcademicReportRequest::TYPE_CLASS => $this->excelClass($filters, $year, $term, $status, $audit),
            AcademicReportRequest::TYPE_SUBJECT => $this->excelSubject($filters, $year, $term, $status, $audit),
            AcademicReportRequest::TYPE_TERM => $this->excelTerm($term, $year, $status, $audit),
            AcademicReportRequest::TYPE_YEAR => $this->excelYear($year, $status, $audit),
            AcademicReportRequest::TYPE_SCHOOL => $this->excelSchool($year, $term, $status, $audit),
            AcademicReportRequest::TYPE_OLEVEL,
            AcademicReportRequest::TYPE_ALEVEL => $this->excelLevel($filters, $year, $term, $status, $audit),
            default => abort(422, 'Unknown report type.'),
        };

        $filename = $this->assetFilename('xlsx', $type, ['year' => $year, 'term' => $term] + $export['context'] ?? []);

        $this->audit($type.'_excel', $audit + ['filename' => $filename]);

        return Excel::download($export['workbook'], $filename);
    }

    protected function excelStudent(array $filters, AcademicYear $year, Term $term, ?string $status, array &$audit)
    {
        $student = $filters['student'] ? Student::with('schoolClass')->findOrFail($filters['student']) : null;

        if (! $student) {
            abort(422, 'Select a student for a student report.');
        }

        $this->authorize('viewStudent', $student);
        $audit += ['student_id' => $student->id];

        return [
            'workbook' => $this->service->excelStudentReport($student, $term, $year, $status),
            'context' => ['student' => $student, 'term' => $term, 'year' => $year],
        ];
    }

    protected function excelClass(array $filters, AcademicYear $year, Term $term, ?string $status, array &$audit)
    {
        $schoolClass = $filters['class'] ? SchoolClass::findOrFail($filters['class']) : null;

        if (! $schoolClass) {
            abort(422, 'Select a class for a class report.');
        }

        $this->authorize('viewClass', $schoolClass);
        $audit += ['class_id' => $schoolClass->id];

        return [
            'workbook' => $this->service->excelClassReport($schoolClass, $term, $year, $status),
            'context' => ['class' => $schoolClass, 'term' => $term, 'year' => $year],
        ];
    }

    protected function excelSubject(array $filters, AcademicYear $year, Term $term, ?string $status, array &$audit)
    {
        $schoolClass = $filters['class'] ? SchoolClass::findOrFail($filters['class']) : null;
        $subject = $filters['subject'] ? Subject::findOrFail($filters['subject']) : null;

        if (! $schoolClass || ! $subject) {
            abort(422, 'Select both class and subject for a subject report.');
        }

        $this->authorize('viewClass', $schoolClass);
        $this->authorize('viewSubject', [$subject, $schoolClass]);
        $audit += ['class_id' => $schoolClass->id, 'subject_id' => $subject->id];

        return [
            'workbook' => $this->service->excelSubjectReport($schoolClass, $term, $year, $subject, $status),
            'context' => ['subject' => $subject, 'class' => $schoolClass, 'term' => $term, 'year' => $year],
        ];
    }

    protected function excelTerm(Term $term, AcademicYear $year, ?string $status, array &$audit)
    {
        $this->authorize('viewTerm', $term);

        return [
            'workbook' => $this->service->excelTermReport($term, $year, $status),
            'context' => ['term' => $term, 'year' => $year],
        ];
    }

    protected function excelYear(AcademicYear $year, ?string $status, array &$audit)
    {
        $this->authorize('viewYear', $year);

        return [
            'workbook' => $this->service->excelYearReport($year, $status),
            'context' => ['year' => $year],
        ];
    }

    protected function excelSchool(AcademicYear $year, ?Term $term, ?string $status, array &$audit)
    {
        return [
            'workbook' => $this->service->excelSchoolReport($year, $term, $status),
            'context' => ['year' => $year, 'term' => $term],
        ];
    }

    protected function excelLevel(array $filters, AcademicYear $year, Term $term, ?string $status, array &$audit)
    {
        if (! $term) {
            abort(422, 'A term is required for a level report.');
        }

        $level = $filters['level'];
        if (! $level || $level === 'all') {
            $level = $filters['report_type'] === AcademicReportRequest::TYPE_ALEVEL ? 'A-Level' : 'O-Level';
        }

        $this->authorize('viewTerm', $term);
        $audit += ['level' => $level];

        return [
            'workbook' => $this->service->excelLevelReport($level, $term, $year, $status),
            'context' => ['level' => $level, 'term' => $term, 'year' => $year],
        ];
    }

    protected function resolveYear(array $filters): AcademicYear
    {
        $year = $filters['year']
            ? AcademicYear::findOrFail($filters['year'])
            : AcademicYear::where('is_current', true)->first();

        if (! $year) {
            abort(422, 'No academic year available.');
        }

        return $year;
    }

    protected function resolveTerm(array $filters, AcademicYear $year): ?Term
    {
        return $filters['term']
            ? Term::findOrFail($filters['term'])
            : $year->terms()->orderBy('term_number')->first();
    }

    /**
     * File names for PDF / Excel assets, e.g.
     * EBSS-Student-Report-{admission_number}-Term-{n}.pdf
     */
    protected function assetFilename(string $extension, string $type, array $data): string
    {
        $year = $data['year'] ?? null;
        $term = $data['term'] ?? null;
        $termLabel = $term ? 'Term-'.(int) $term->term_number : '';
        $yearSegment = $year ? $this->safeSegment($year?->name ?? '') : (string) date('Y');

        if ($type === AcademicReportRequest::TYPE_STUDENT_CARD) {
            $admission = $data['student']?->admission_number ?? 'STUDENT';

            return 'EBSS-Student-Report-'.$this->safeSegment($admission).($termLabel ? '-'.$termLabel : '').'.'.$extension;
        }

        if ($type === AcademicReportRequest::TYPE_CLASS) {
            $class = $this->safeSegment($data['class']?->name ?? 'Class');

            return 'EBSS-Class-Report-'.$class.($termLabel ? '-'.$termLabel : '').'-'.$yearSegment.'.'.$extension;
        }

        if ($type === AcademicReportRequest::TYPE_SUBJECT) {
            $subject = $this->safeSegment($data['subject']?->code ?? 'Subject');
            $class = $this->safeSegment($data['class']?->name ?? 'Class');

            return 'EBSS-Subject-Report-'.$subject.'-'.$class.($termLabel ? '-'.$termLabel : '').'-'.$yearSegment.'.'.$extension;
        }

        if ($type === AcademicReportRequest::TYPE_TERM) {
            return 'EBSS-Term-Report-'.($termLabel ?: 'Term').'-'.$yearSegment.'.'.$extension;
        }

        if ($type === AcademicReportRequest::TYPE_YEAR) {
            return 'EBSS-Annual-Report-'.$yearSegment.'.'.$extension;
        }

        if ($type === AcademicReportRequest::TYPE_SCHOOL) {
            return 'EBSS-School-Report-'.$yearSegment.($termLabel ? '-'.$termLabel : '').'.'.$extension;
        }

        if (in_array($type, [AcademicReportRequest::TYPE_OLEVEL, AcademicReportRequest::TYPE_ALEVEL], true)) {
            $level = $data['level'] ?? ($type === AcademicReportRequest::TYPE_ALEVEL ? 'A-Level' : 'O-Level');

            return 'EBSS-'.$this->safeSegment($level).'-Report-'.($termLabel ?: 'Term').'-'.$yearSegment.'.'.$extension;
        }

        return 'EBSS-Report-'.$this->safeSegment($type).($termLabel ? '-'.$termLabel : '').'-'.$yearSegment.'.'.$extension;
    }

    protected function safeSegment(string $value): string
    {
        $value = preg_replace('/[^A-Za-z0-9-]+/', '-', $value);

        return trim((string) $value, '-') ?: 'NA';
    }

    /**
     * Student's own published reports.
     */
    public function myReports(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isStudent() && $user->student, 403);

        $results = $user->student
            ->results()
            ->with(['term', 'academicYear', 'schoolClass', 'items'])
            ->where('status', Result::STATUS_PUBLISHED)
            ->orderByDesc('academic_year_id')
            ->orderByDesc('term_id')
            ->get();

        return view('student.reports.index', [
            'student' => $user->student,
            'results' => $results,
        ]);
    }

    /**
     * Student report detail. Accessible by the student, staff or linked parent.
     */
    public function showStudentReport(Student $student, Term $term, AcademicYear $year, Request $request)
    {
        $this->authorize('viewStudent', $student);

        if ($request->user()->isStudent() && $student->id !== $request->user()->student?->id) {
            abort(403);
        }

        if ($request->user()->isParent()) {
            abort_unless($student->parent_id === $request->user()->parent?->id, 403);
        }

        $status = $request->user()->hasRole('admin', 'teacher')
            ? $request->string('status')->toString() ?: null
            : Result::STATUS_PUBLISHED;

        $data = $this->service->studentReportCard($student, $term, $year, $status);
        $this->audit('student_report_viewed', [
            'student_id' => $student->id,
            'term_id' => $term->id,
            'year_id' => $year->id,
        ]);

        $pdfUrl = $request->user()->hasRole('admin', 'teacher')
            ? $this->reportRoute('pdf', [
                'report_type' => AcademicReportRequest::TYPE_STUDENT_CARD,
                'year' => $year->id,
                'term' => $term->id,
                'student' => $student->id,
                ...($status ? ['status' => $status] : []),
            ])
            : route('student.reports.pdf', [$student, $term, $year]);

        return view('reports.academic.student-report', $data + [
            'pdfUrl' => $pdfUrl,
        ]);
    }

    /**
     * Student's own published report as a download (IDOR-guarded).
     */
    public function pdfStudentReport(Student $student, Term $term, AcademicYear $year, Request $request)
    {
        abort_unless($request->user()?->isStudent(), 403);
        abort_unless($student->id === $request->user()->student?->id, 403);

        $this->authorize('viewStudent', $student);

        $data = $this->service->studentReportCard(
            $student,
            $term,
            $year,
            Result::STATUS_PUBLISHED
        );

        $filename = 'EBSS-Student-Report-'.$this->safeSegment($student->admission_number).'-Term-'.(int) $term->term_number.'.pdf';

        $this->audit('student_report_pdf', [
            'student_id' => $student->id,
            'term_id' => $term->id,
            'year_id' => $year->id,
            'filename' => $filename,
        ]);

        $pdf = app(ReportPdfService::class)->generate('reports.academic.student-report', $data);

        return response($pdf, 200, app(ReportPdfService::class)->headers($filename));
    }

    /**
     * Parent's children with published reports.
     */
    public function childReports(Request $request)
    {
        $user = $request->user();
        abort_unless($user?->isParent() && $user->parent, 403);

        $children = $user->parent->students()
            ->with([
                'schoolClass',
                'results' => fn ($query) => $query
                    ->where('status', Result::STATUS_PUBLISHED)
                    ->with(['term', 'academicYear', 'items']),
            ])
            ->orderBy('first_name')
            ->get();

        return view('parent.reports.index', compact('children'));
    }

    /**
     * Parent view of a child's report.
     */
    public function showChildReport(Student $student, Term $term, AcademicYear $year, Request $request)
    {
        abort_unless($request->user()?->isParent(), 403);
        abort_unless($student->parent_id === $request->user()->parent?->id, 403);

        $this->authorize('viewStudent', $student);

        $data = $this->service->studentReportCard(
            $student,
            $term,
            $year,
            Result::STATUS_PUBLISHED
        );

        $this->audit('child_report_viewed', [
            'student_id' => $student->id,
            'term_id' => $term->id,
            'year_id' => $year->id,
        ]);

        return view('reports.academic.student-report', $data + [
            'pdfUrl' => route('parent.reports.pdf', [$student, $term, $year]),
        ]);
    }

    /**
     * Parent download of a child's published report (IDOR-guarded).
     */
    public function pdfChildReport(Student $student, Term $term, AcademicYear $year, Request $request)
    {
        abort_unless($request->user()?->isParent(), 403);
        abort_unless($student->parent_id === $request->user()->parent?->id, 403);

        $this->authorize('viewStudent', $student);

        $data = $this->service->studentReportCard(
            $student,
            $term,
            $year,
            Result::STATUS_PUBLISHED
        );

        $filename = 'EBSS-Student-Report-'.$this->safeSegment($student->admission_number).'-Term-'.(int) $term->term_number.'.pdf';

        $this->audit('child_report_pdf', [
            'student_id' => $student->id,
            'term_id' => $term->id,
            'year_id' => $year->id,
            'filename' => $filename,
        ]);

        $pdf = app(ReportPdfService::class)->generate('reports.academic.student-report', $data);

        return response($pdf, 200, app(ReportPdfService::class)->headers($filename));
    }

    /**
     * Normalize alternate query parameter names into one internal structure.
     */
    protected function normalize(AcademicReportRequest $request): array
    {
        $type = $request->input('report_type', $request->input('type', AcademicReportRequest::TYPE_STUDENT_CARD));

        return [
            'report_type' => $type,
            'year' => $request->integer('academic_year_id', $request->integer('year')) ?: null,
            'term' => $request->integer('term_id', $request->integer('term')) ?: null,
            'class' => $request->integer('school_class_id', $request->integer('class')) ?: null,
            'subject' => $request->integer('subject_id', $request->integer('subject')) ?: null,
            'student' => $request->integer('student_id', $request->integer('student')) ?: null,
            'level' => $request->input('level'),
            'status' => $request->input('status') ?: 'all',
        ];
    }

    /**
     * Build the requested report and return view/data/audit metadata.
     */
    public function buildReportPublic(string $type, array $filters, AcademicYear $year, ?Term $term): array
    {
        return $this->buildReport($type, $filters, $year, $term);
    }

    protected function buildReport(string $type, array $filters, AcademicYear $year, ?Term $term): array
    {
        return match ($type) {
            AcademicReportRequest::TYPE_STUDENT_CARD => $this->buildStudentReport($filters, $year, $term),
            AcademicReportRequest::TYPE_CLASS => $this->buildClassReport($filters, $year, $term),
            AcademicReportRequest::TYPE_SUBJECT => $this->buildSubjectReport($filters, $year, $term),
            AcademicReportRequest::TYPE_TERM => $this->buildTermReport($filters, $year, $term),
            AcademicReportRequest::TYPE_YEAR => $this->buildYearReport($filters, $year),
            AcademicReportRequest::TYPE_SCHOOL => $this->buildSchoolReport($filters, $year, $term),
            AcademicReportRequest::TYPE_OLEVEL,
            AcademicReportRequest::TYPE_ALEVEL => $this->buildLevelReport($filters, $year, $term),
            default => abort(422, 'Unknown report type.'),
        };
    }

    protected function buildStudentReport(array $filters, AcademicYear $year, ?Term $term): array
    {
        if (! $term) {
            abort(422, 'A term is required for a student report.');
        }

        $student = $filters['student']
            ? Student::with('schoolClass')->findOrFail($filters['student'])
            : null;

        if (! $student) {
            abort(422, 'Select a student for a student report.');
        }

        $this->authorize('viewStudent', $student);

        $data = $this->service->studentReportCard($student, $term, $year, $filters['status']);

        return [
            'view' => 'reports.academic.student-report',
            'data' => $data,
            'audit' => [
                'student_id' => $student->id,
                'term_id' => $term->id,
                'year_id' => $year->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function buildClassReport(array $filters, AcademicYear $year, ?Term $term): array
    {
        if (! $term) {
            abort(422, 'A term is required for a class report.');
        }

        $schoolClass = $filters['class']
            ? SchoolClass::findOrFail($filters['class'])
            : null;

        if (! $schoolClass) {
            abort(422, 'Select a class for a class report.');
        }

        $this->authorize('viewClass', $schoolClass);

        return [
            'view' => 'reports.academic.class-report',
            'data' => $this->service->classResults($schoolClass, $term, $year, $filters['status']),
            'audit' => [
                'class_id' => $schoolClass->id,
                'term_id' => $term->id,
                'year_id' => $year->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function buildSubjectReport(array $filters, AcademicYear $year, ?Term $term): array
    {
        if (! $term) {
            abort(422, 'A term is required for a subject report.');
        }

        $schoolClass = $filters['class']
            ? SchoolClass::findOrFail($filters['class'])
            : null;
        $subject = $filters['subject']
            ? Subject::findOrFail($filters['subject'])
            : null;

        if (! $schoolClass || ! $subject) {
            abort(422, 'Select both class and subject for a subject report.');
        }

        $this->authorize('viewClass', $schoolClass);
        $this->authorize('viewSubject', [$subject, $schoolClass]);

        return [
            'view' => 'reports.academic.subject-report',
            'data' => $this->service->subjectPerformance($schoolClass, $term, $year, $subject, $filters['status']),
            'audit' => [
                'class_id' => $schoolClass->id,
                'subject_id' => $subject->id,
                'term_id' => $term->id,
                'year_id' => $year->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function buildTermReport(array $filters, AcademicYear $year, ?Term $term): array
    {
        if (! $term) {
            abort(422, 'A term is required for a term report.');
        }

        $this->authorize('viewTerm', $term);

        return [
            'view' => 'reports.academic.term-report',
            'data' => $this->service->termPerformance($term, $year, $filters['status']),
            'audit' => [
                'term_id' => $term->id,
                'year_id' => $year->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function buildYearReport(array $filters, AcademicYear $year): array
    {
        $this->authorize('viewYear', $year);

        return [
            'view' => 'reports.academic.annual-report',
            'data' => $this->service->academicYearPerformance($year, $filters['status']),
            'audit' => [
                'year_id' => $year->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function buildSchoolReport(array $filters, AcademicYear $year, ?Term $term): array
    {
        return [
            'view' => 'reports.academic.school-report',
            'data' => $this->service->schoolPerformance($year, $term, $filters['status']),
            'audit' => [
                'year_id' => $year->id,
                'term_id' => $term?->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function buildLevelReport(array $filters, AcademicYear $year, ?Term $term): array
    {
        if (! $term) {
            abort(422, 'A term is required for a level report.');
        }

        $level = $filters['level'];
        if (! $level || $level === 'all') {
            $level = $filters['report_type'] === AcademicReportRequest::TYPE_ALEVEL ? 'A-Level' : 'O-Level';
        }

        $expectedType = $level === 'A-Level'
            ? AcademicReportRequest::TYPE_ALEVEL
            : AcademicReportRequest::TYPE_OLEVEL;

        if ($filters['report_type'] !== $expectedType) {
            abort(422, 'Invalid level report configuration.');
        }

        $this->authorize('viewTerm', $term);

        return [
            'view' => 'reports.academic.level-report',
            'data' => $this->service->levelPerformance($level, $term, $year, $filters['status']),
            'audit' => [
                'level' => $level,
                'term_id' => $term->id,
                'year_id' => $year->id,
                'status' => $filters['status'],
            ],
        ];
    }

    protected function audit(string $action, array $meta = []): void
    {
        AuditLogService::log(
            $action,
            'Academic report action: '.$action,
            null,
            $meta
        );
    }
}
