<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Services\AttendanceService;
use App\Services\GradingService;

class DashboardController extends Controller
{
    public function index(GradingService $gradingService, AttendanceService $attendanceService)
    {
        $user = auth()->user();
        $student = $user->student;

        $academicYear = AcademicYear::where('is_current', true)->first();
        $latestResult = $gradingService->latestResult($student);
        $attendance = $attendanceService->monthlySummary($student->id, $academicYear?->id);

        $totalBalance = (float) $student->studentFees->sum('balance');

        return view('student.dashboard', compact(
            'student',
            'latestResult',
            'attendance',
            'totalBalance',
            'academicYear',
        ));
    }
}
