<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = Auth::user()->teacher;
        abort_if(! $teacher, 403, 'No teacher profile is linked to your account.');

        $classes = $teacher->classes()->withCount('students')->orderBy('form_number')->get();
        $subjectCount = $teacher->subjects()->distinct()->count('subjects.id');
        $studentCount = $classes->sum('students_count');
        $announcements = Announcement::where('is_active', true)
            ->latest('published_at')
            ->limit(4)
            ->get();

        return view('teacher.dashboard', compact('teacher', 'classes', 'subjectCount', 'studentCount', 'announcements'));
    }
}
