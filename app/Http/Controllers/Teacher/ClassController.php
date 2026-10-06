<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ClassController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        abort_if(! $teacher, 403, 'No teacher profile is linked to your account.');

        $classes = $teacher->classes()
            ->withCount('students')
            ->orderBy('form_number')
            ->get();

        return view('teacher.classes', compact('teacher', 'classes'));
    }
}
