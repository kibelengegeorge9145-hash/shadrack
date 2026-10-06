<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Services\AuditLogService;
use App\Services\FeeService;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function index()
    {
        $feeStructures = FeeStructure::with('schoolClass', 'academicYear')
            ->latest()
            ->get();

        $categories = FeeStructure::CATEGORIES;
        $levels = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];
        $classes = SchoolClass::orderBy('name')->pluck('name', 'id');
        $academicYears = AcademicYear::orderByDesc('start_date')->pluck('name', 'id');

        return view('admin.fees', compact('feeStructures', 'categories', 'levels', 'classes', 'academicYears'));
    }

    public function store(Request $request, FeeService $feeService)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:'.implode(',', FeeStructure::CATEGORIES)],
            'amount' => ['required', 'numeric', 'min:0'],
            'level' => ['nullable', 'in:O-Level,A-Level'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'sync_students' => ['nullable', 'boolean'],
        ]);

        $structure = FeeStructure::create([
            'name' => $data['name'],
            'category' => $data['category'],
            'amount' => $data['amount'],
            'level' => $data['level'] ?? null,
            'school_class_id' => $data['school_class_id'] ?? null,
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($request->boolean('sync_students')) {
            $this->syncStudents($structure, $feeService);
        }

        AuditLogService::log(
            'fee_structure_created',
            "Fee structure '{$structure->name}' ({$structure->category}) created",
            $structure,
            ['amount' => (float) $structure->amount, 'level' => $structure->level]
        );

        return redirect()->route('admin.fees.index')
            ->with('success', 'Fee structure created successfully.');
    }

    protected function syncStudents(FeeStructure $structure, FeeService $feeService): void
    {
        $query = Student::active();

        if ($structure->school_class_id) {
            $query->where('school_class_id', $structure->school_class_id);
        }

        if ($structure->level) {
            $query->where('level', $structure->level);
        }

        foreach ($query->get() as $student) {
            $feeService->syncStudentFees($student, $structure->academic_year_id, null);
        }
    }
}
