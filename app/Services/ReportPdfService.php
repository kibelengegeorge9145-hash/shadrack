<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Generates PDF binaries from the structured report Blade views.
 *
 * Views are rendered with `pdfMode` enabled so they inherit the dedicated
 * `reports.pdf` layout (no dashboard chrome, no external resources) instead
 * of the on-screen report layout.
 */
class ReportPdfService
{
    /**
     * @param  array<int, array{key: string, value: int|string}>  $options
     */
    public function generate(string $view, array $data, array $options = []): string
    {
        $data['pdfMode'] = true;

        $pdf = Pdf::loadView($view, $data);
        $pdf->setPaper($options['paper'] ?? 'a4', $options['orientation'] ?? 'portrait');

        return $pdf->output();
    }

    public function headers(string $filename): array
    {
        return [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ];
    }
}
