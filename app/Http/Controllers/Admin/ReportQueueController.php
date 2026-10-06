<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateReportJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReportQueueController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        Gate::authorize('generate-academic-reports');

        $validated = $request->validate([
            'report_type' => ['required', 'string', 'max:30'],
            'academic_year_id' => ['nullable', 'integer', 'exists:academic_years,id'],
            'term_id' => ['nullable', 'integer', 'exists:terms,id'],
            'school_class_id' => ['nullable', 'integer', 'exists:school_classes,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'format' => ['required', 'in:pdf'],
        ]);

        $filters = [
            'report_type' => $validated['report_type'],
            'year' => $validated['academic_year_id'] ?? null,
            'term' => $validated['term_id'] ?? null,
            'class' => $validated['school_class_id'] ?? null,
            'subject' => $validated['subject_id'] ?? null,
            'student' => $validated['student_id'] ?? null,
            'status' => $request->input('status', 'all'),
        ];

        GenerateReportJob::dispatch($filters, 'pdf', $request->user()->id, $request->user()->role);

        return response()->json([
            'message' => 'Report queued for generation.',
        ], 202);
    }
}
