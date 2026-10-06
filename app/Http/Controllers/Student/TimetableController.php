<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Timetable;

class TimetableController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        $entries = Timetable::where('school_class_id', $student->school_class_id)
            ->with(['subject', 'teacher'])
            ->orderByRaw("FIELD(day, 'monday','tuesday','wednesday','thursday','friday','saturday','sunday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        return view('student.timetable', compact('student', 'entries'));
    }
}
