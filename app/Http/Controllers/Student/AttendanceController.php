<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\AttendanceService;

class AttendanceController extends Controller
{
    public function index(AttendanceService $attendanceService)
    {
        $user = auth()->user();
        $student = $user->student;

        $academicYear = AcademicYear::where('is_current', true)->first();
        $summary = $attendanceService->monthlySummary($student->id, $academicYear?->id);

        return view('student.attendance', compact('student', 'summary', 'academicYear'));
    }
}
