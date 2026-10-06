<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Term;
use App\Services\AttendanceService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Auth::user()->teacher;
        abort_if(! $teacher, 403, 'No teacher profile is linked to your account.');

        $classes = $teacher->classes()->orderBy('form_number')->get();
        $date = $request->filled('date') ? $request->date : now()->toDateString();

        $selectedClass = null;
        $students = collect();
        $existing = collect();

        if ($request->filled('school_class_id')) {
            $selectedClass = $classes->firstWhere('id', (int) $request->school_class_id);
            abort_if(! $selectedClass, 403);

            $students = Student::active()
                ->where('school_class_id', $selectedClass->id)
                ->orderBy('first_name')
                ->get();

            $existing = Attendance::where('date', $date)
                ->whereIn('student_id', $students->pluck('id'))
                ->get()
                ->keyBy('student_id');
        }

        return view('teacher.attendance', compact('teacher', 'classes', 'selectedClass', 'students', 'existing', 'date'));
    }

    public function store(Request $request, AttendanceService $attendance)
    {
        $data = $request->validate([
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'date' => ['required', 'date'],
            'status' => ['required', 'array'],
            'status.*' => ['required', 'in:present,absent,late,excused'],
        ]);

        $teacher = Auth::user()->teacher;
        abort_if(! $teacher, 403, 'No teacher profile is linked to your account.');

        $class = SchoolClass::findOrFail($data['school_class_id']);
        abort_if(! $teacher->classes()->whereKey($class->id)->exists(), 403, 'You are not assigned to this class.');

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
