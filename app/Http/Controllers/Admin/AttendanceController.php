<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use App\Services\AttendanceService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('form_number')->get();
        $selectedClass = $request->filled('school_class_id') ? SchoolClass::find($request->school_class_id) : null;
        $date = $request->filled('date') ? $request->date : now()->toDateString();

        $students = collect();
        $existing = collect();

        if ($selectedClass) {
            $students = Student::active()
                ->where('school_class_id', $selectedClass->id)
                ->orderBy('first_name')
                ->get();

            $existing = Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('admin.attendance', compact('classes', 'selectedClass', 'date', 'students', 'existing'));
    }

    public function store(Request $request, AttendanceService $attendance)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:present,absent,late,excused'],
        ]);

        $class = SchoolClass::findOrFail($data['school_class_id']);
        $year = AcademicYear::where('is_current', true)->first();
        $term = Term::where('is_current', true)->first();

        $count = $attendance->saveDaily($data['status'], $class->id, $year?->id, $term?->id, $data['date']);

        AuditLogService::log(
            'attendance.saved',
            "Saved attendance for {$count} student(s) in {$class->name} on {$data['date']}",
            $class,
        );

        return back()->with('success', "Attendance saved for {$count} student(s) on {$data['date']}.");
    }
}
