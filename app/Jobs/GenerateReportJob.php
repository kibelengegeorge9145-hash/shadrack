<?php

namespace App\Jobs;

use App\Http\Controllers\AcademicReportController;
use App\Http\Requests\AcademicReportRequest;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\AcademicReportService;
use App\Services\AuditLogService;
use App\Services\ReportPdfService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class GenerateReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $timeout = 120;

    /**
     * @param  array<string,mixed>  $filters  Normalized report filters (report_type, year, term, class …).
     * @param  string  $format  'pdf' | 'xlsx'.
     * @param  int  $userId  Dispatching user (for storage ownership + audit).
     * @param  string  $role  User role (admin|teacher).
     */
    public function __construct(
        public array $filters,
        public string $format,
        public int $userId,
        public string $role,
    ) {
        $this->onQueue('reports');
    }

    public function handle(): string
    {
        $service = app(AcademicReportService::class);
        $type = $this->filters['report_type'];

        $year = $this->filters['year']
            ? AcademicYear::findOrFail($this->filters['year'])
            : AcademicYear::where('is_current', true)->first();

        if (! $year) {
            abort(422, 'No academic year available.');
        }

        $term = $this->filters['term']
            ? Term::findOrFail($this->filters['term'])
            : $year->terms()->orderBy('term_number')->first();

        // Resolve data + view via the controller's buildReport path (duplicated for queue context)
        $report = app(AcademicReportController::class)->buildReportPublic(
            $type,
            $this->filters,
            $year,
            $term
        );

        $filename = $this->buildFilename($type, $report, $year, $term);
        $dir = "reports/{$this->userId}";
        $path = "{$dir}/{$filename}";

        if ($this->format === 'pdf') {
            $pdf = app(ReportPdfService::class)->generate($report['view'], $report['data']);
            Storage::disk('local')->put($path, $pdf);
        } else {
            // Excel export not queued for simplicity — handled synchronously.
            abort(422, 'Excel batch export is not yet supported via queue.');
        }

        AuditLogService::log(
            'report_queued_generated',
            "Queued report generated: {$filename}",
            null,
            [
                'report_type' => $type,
                'format' => $this->format,
                'filename' => $filename,
                'path' => $path,
            ]
        );

        return $path;
    }

    protected function buildFilename(string $type, array $report, AcademicYear $year, ?Term $term): string
    {
        $termLabel = $term ? 'Term-'.(int) $term->term_number : '';
        $yearSegment = preg_replace('/[^A-Za-z0-9-]+/', '-', $year->name ?? date('Y'));
        $yearSegment = trim($yearSegment, '-') ?: date('Y');

        $safe = fn (string $v) => trim((string) preg_replace('/[^A-Za-z0-9-]+/', '-', $v), '-') ?: 'NA';

        return match ($type) {
            AcademicReportRequest::TYPE_STUDENT_CARD => 'EBSS-Student-Report-'.$safe($report['data']['student']->admission_number ?? 'STUDENT').($termLabel ? '-'.$termLabel : '').'.pdf',
            default => 'EBSS-Report-'.$safe($type).($termLabel ? '-'.$termLabel : '').'-'.$yearSegment.'.pdf',
        };
    }
}
