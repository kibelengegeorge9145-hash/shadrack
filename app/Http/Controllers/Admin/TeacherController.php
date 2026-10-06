<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TeacherController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $query = Teacher::with(['subjects', 'classes'])->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('staff_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $teachers = $query->paginate(15)->withQueryString();

        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->pluck('name', 'id');

        return view('admin.teachers.create', compact('subjects', 'classes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'staff_id' => ['nullable', 'string', 'max:255', 'unique:teachers,staff_id'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'department' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'classes' => ['nullable', 'array'],
            'classes.*' => ['exists:school_classes,id'],
        ]);

        $staffId = $data['staff_id'] ?: $this->authService->nextStaffId();

        $user = User::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'name' => trim(implode(' ', array_filter([
                $data['first_name'], $data['middle_name'] ?? null, $data['last_name'],
            ]))),
            'username' => Str::slug($data['first_name'].'-'.$data['last_name'].'-'.Str::random(4), '-'),
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'role' => User::ROLE_TEACHER,
            'staff_id' => $staffId,
            'password' => $this->authService->temporaryPassword(),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $teacher = Teacher::create([
            'user_id' => $user->id,
            'staff_id' => $staffId,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'department' => $data['department'] ?? null,
            'qualification' => $data['qualification'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if (! empty($data['subjects'])) {
            $teacher->subjects()->sync($data['subjects']);
        }

        if (! empty($data['classes'])) {
            $teacher->classes()->sync($data['classes']);
        }

        AuditLogService::log('teacher_created', "Teacher {$teacher->full_name} ({$staffId}) created", $teacher);

        return redirect()->route('admin.teachers.index')
            ->with('success', "Teacher created successfully. Username: {$user->username}");
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['user', 'subjects', 'classes']);

        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $subjects = Subject::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $selectedSubjects = $teacher->subjects()->pluck('subjects.id')->all();
        $selectedClasses = $teacher->classes()->pluck('school_classes.id')->all();

        return view('admin.teachers.edit', compact('teacher', 'subjects', 'classes', 'selectedSubjects', 'selectedClasses'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'staff_id' => ['required', 'string', 'max:255', 'unique:teachers,staff_id,'.$teacher->id],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'department' => ['nullable', 'string', 'max:255'],
            'qualification' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'classes' => ['nullable', 'array'],
            'classes.*' => ['exists:school_classes,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $teacher->update([
            'staff_id' => $data['staff_id'],
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'] ?? null,
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'] ?? null,
            'department' => $data['department'] ?? null,
            'qualification' => $data['qualification'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        if ($teacher->user) {
            $teacher->user->update([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'name' => trim(implode(' ', array_filter([
                    $data['first_name'], $data['middle_name'] ?? null, $data['last_name'],
                ]))),
                'staff_id' => $data['staff_id'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if (! empty($data['password'])) {
                $teacher->user->update([
                    'password' => $data['password'],
                    'must_change_password' => false,
                ]);
            }
        }

        if (array_key_exists('subjects', $data)) {
            $teacher->subjects()->sync($data['subjects'] ?? []);
        }

        if (array_key_exists('classes', $data)) {
            $teacher->classes()->sync($data['classes'] ?? []);
        }

        AuditLogService::log('teacher_updated', "Teacher {$teacher->full_name} ({$data['staff_id']}) updated", $teacher);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        $name = $teacher->full_name;

        if ($teacher->user) {
            $teacher->user->update(['is_active' => false]);
        }

        $teacher->delete();

        AuditLogService::log('teacher_deleted', "Teacher {$name} removed", $teacher);

        return redirect()->route('admin.teachers.index')
            ->with('success', 'Teacher removed successfully.');
    }
}
