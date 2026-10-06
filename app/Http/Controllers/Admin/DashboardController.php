<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\MyParent;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::active()->count();
        $totalTeachers = Teacher::count();
        $totalParents = MyParent::count();
        $totalClasses = SchoolClass::count();

        $classesWithCounts = SchoolClass::withCount('students')
            ->latest()
            ->get();

        $recentResults = Result::with('student', 'schoolClass', 'term')
            ->latest()
            ->limit(5)
            ->get();

        $recentAnnouncements = Announcement::latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalParents',
            'totalClasses',
            'classesWithCounts',
            'recentResults',
            'recentAnnouncements',
        ));
    }
}
