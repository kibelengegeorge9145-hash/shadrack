<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::with('terms')->orderByDesc('start_date')->get();
        $currentYear = AcademicYear::where('is_current', true)->first();

        return view('admin.settings', compact('academicYears', 'currentYear'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'term_id' => ['nullable', 'exists:terms,id'],
        ]);

        AcademicYear::query()->update(['is_current' => false]);
        AcademicYear::where('id', $data['academic_year_id'])->update(['is_current' => true]);

        Term::query()->update(['is_current' => false]);
        if (! empty($data['term_id'])) {
            $term = Term::where('id', $data['term_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->first();

            if ($term) {
                $term->update(['is_current' => true]);

                AuditLogService::log(
                    'settings_updated',
                    "Active academic year set to {$term->academicYear?->name} / {$term->name}",
                    $term
                );

                return redirect()->route('admin.settings.index')
                    ->with('success', 'Active academic year and term updated successfully.');
            }
        }

        $year = AcademicYear::find($data['academic_year_id']);

        AuditLogService::log(
            'settings_updated',
            "Active academic year set to {$year->name}",
            $year
        );

        return redirect()->route('admin.settings.index')
            ->with('success', 'Active academic year updated successfully.');
    }
}
