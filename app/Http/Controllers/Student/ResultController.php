<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Result;

class ResultController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        $results = Result::published()
            ->where('student_id', $student->id)
            ->with(['items.subject', 'term', 'academicYear'])
            ->latest('published_at')
            ->get();

        return view('student.results', compact('student', 'results'));
    }
}
