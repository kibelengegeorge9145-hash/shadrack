<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Teacher;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $query = SchoolClass::withCount(['students'])->with('classTeacher')->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('label', 'like', "%{$search}%")
                    ->orWhere('academic_year', 'like', "%{$search}%");
            });
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        $classes = $query->paginate(15)->withQueryString();

        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.classes.index', compact('classes', 'levelOptions'));
    }

    public function create()
    {
        $teachers = Teacher::where('is_active', true)->orderBy('last_name')->get()->pluck('full_name', 'id');
        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.classes.create', compact('teachers', 'levelOptions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:O-Level,A-Level'],
            'form_number' => ['nullable', 'integer', 'min:1'],
            'label' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:255'],
            'class_teacher_id' => ['nullable', 'exists:teachers,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $class = SchoolClass::create([
            'name' => $data['name'],
            'level' => $data['level'],
            'form_number' => $data['form_number'] ?? null,
            'label' => $data['label'] ?? null,
            'academic_year' => $data['academic_year'] ?? now()->year.'/'.(now()->year + 1),
            'class_teacher_id' => $data['class_teacher_id'] ?? null,
            'description' => $data['description'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLogService::log('class_created', "Class {$data['name']} created", $class);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function edit(SchoolClass $class)
    {
        $teachers = Teacher::where('is_active', true)->orderBy('last_name')->get()->pluck('full_name', 'id');
        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.classes.edit', compact('class', 'teachers', 'levelOptions'));
    }

    public function update(Request $request, SchoolClass $class)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'level' => ['required', 'in:O-Level,A-Level'],
            'form_number' => ['nullable', 'integer', 'min:1'],
            'label' => ['nullable', 'string', 'max:255'],
            'academic_year' => ['nullable', 'string', 'max:255'],
            'class_teacher_id' => ['nullable', 'exists:teachers,id'],
            'description' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $class->update($data);

        AuditLogService::log('class_updated', "Class {$data['name']} updated", $class);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(SchoolClass $class)
    {
        $name = $class->name;

        $class->delete();

        AuditLogService::log('class_deleted', "Class {$name} removed", $class);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class removed successfully.');
    }
}
