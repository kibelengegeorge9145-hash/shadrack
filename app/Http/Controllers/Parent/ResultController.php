<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Result;

class ResultController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $parent = $user->parent;
        $childIds = $parent->students()->pluck('students.id');

        $results = Result::published()
            ->whereIn('student_id', $childIds)
            ->with(['student', 'items.subject', 'term', 'academicYear'])
            ->latest('published_at')
            ->get()
            ->groupBy('student_id');

        $children = $parent->students()->with('schoolClass')->get();

        return view('parent.results', compact('parent', 'results', 'children'));
    }
}
