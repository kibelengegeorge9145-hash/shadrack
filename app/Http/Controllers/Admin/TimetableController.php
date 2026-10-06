<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Timetable;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $classes = SchoolClass::orderBy('form_number')->get();
        $days = Timetable::DAYS;
        $teachers = Teacher::orderBy('first_name')->get()->pluck('full_name', 'id');

        $selectedClass = $request->filled('school_class_id') ? SchoolClass::find($request->school_class_id) : null;

        $subjects = Subject::query()
            ->when($selectedClass, fn ($q) => $q->where('level', $selectedClass->level))
            ->orderBy('name')
            ->pluck('name', 'id');

        $entries = collect();
        if ($selectedClass) {
            $entries = Timetable::with(['subject', 'teacher'])
                ->where('school_class_id', $selectedClass->id)
                ->get()
                ->groupBy('day');
        }

        return view('admin.timetable', compact('classes', 'days', 'subjects', 'teachers', 'selectedClass', 'entries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'day' => ['required', 'in:'.implode(',', Timetable::DAYS)],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'subject_id' => ['nullable', 'exists:subjects,id'],
            'teacher_id' => ['nullable', 'exists:teachers,id'],
            'room' => ['nullable', 'string', 'max:100'],
        ]);

        $entry = Timetable::create($data);

        AuditLogService::log(
            'timetable.created',
            "Added timetable entry for {$entry->schoolClass->name}",
            $entry,
        );

        return back()->with('success', 'Timetable entry added successfully.');
    }
}
