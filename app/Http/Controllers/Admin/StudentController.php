<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\MyParent;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index(Request $request)
    {
        $query = Student::with(['schoolClass', 'parent'])->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('admission_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%");
            });
        }

        if ($level = $request->get('level')) {
            $query->where('level', $level);
        }

        if ($class = $request->get('class')) {
            $query->where('school_class_id', $class);
        }

        $students = $query->paginate(15)->withQueryString();

        $classes = SchoolClass::orderBy('name')->pluck('name', 'id');

        $levelOptions = ['O-Level' => 'O-Level', 'A-Level' => 'A-Level'];

        return view('admin.students.index', compact('students', 'classes', 'levelOptions'));
    }

    public function create()
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $parents = MyParent::orderBy('last_name')->get()->pluck('full_name', 'id');
        $subjects = Subject::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $academicYear = $this->currentAcademicYear();

        return view('admin.students.create', compact('classes', 'parents', 'subjects', 'academicYear'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'admission_number' => ['nullable', 'string', 'max:255', 'unique:students,admission_number'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'level' => ['required', 'in:O-Level,A-Level'],
            'parent_id' => ['nullable', 'exists:parents,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive,graduated,transferred'],
            'enrolled_on' => ['nullable', 'date'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
        ]);

        $admissionNumber = ! empty($data['admission_number']) ? $data['admission_number'] : $this->authService->nextAdmissionNumber();

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
            'role' => User::ROLE_STUDENT,
            'admission_number' => $admissionNumber,
            'password' => $this->authService->temporaryPassword(),
            'must_change_password' => true,
            'is_active' => true,
        ]);

        $student = Student::create([
            'user_id' => $user->id,
            'admission_number' => $admissionNumber,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'date_of_birth' => $data['date_of_birth'],
            'gender' => $data['gender'],
            'school_class_id' => $data['school_class_id'],
            'level' => $data['level'],
            'parent_id' => $data['parent_id'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'status' => $data['status'],
            'enrolled_on' => $data['enrolled_on'] ?? now()->toDateString(),
        ]);

        if (! empty($data['subjects'])) {
            $this->syncSubjects($student, $data['subjects'], $data['school_class_id']);
        }

        AuditLogService::log('student_created', "Student {$student->full_name} ({$admissionNumber}) created", $student);

        return redirect()->route('admin.students.index')
            ->with('success', "Student created successfully. Username: {$user->username}");
    }

    public function show(Student $student)
    {
        $student->load(['schoolClass', 'parent', 'subjects', 'results']);

        return view('admin.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $classes = SchoolClass::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $parents = MyParent::orderBy('last_name')->get()->pluck('full_name', 'id');
        $subjects = Subject::where('is_active', true)->orderBy('name')->pluck('name', 'id');
        $academicYear = $this->currentAcademicYear();
        $selectedSubjects = $student->subjects()->pluck('subjects.id')->all();

        return view('admin.students.edit', compact('student', 'classes', 'parents', 'subjects', 'academicYear', 'selectedSubjects'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'admission_number' => ['required', 'string', 'max:255', 'unique:students,admission_number,'.$student->id],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'school_class_id' => ['required', 'exists:school_classes,id'],
            'level' => ['required', 'in:O-Level,A-Level'],
            'parent_id' => ['nullable', 'exists:parents,id'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'status' => ['required', 'in:active,inactive,graduated,transferred'],
            'enrolled_on' => ['nullable', 'date'],
            'subjects' => ['nullable', 'array'],
            'subjects.*' => ['exists:subjects,id'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $student->update($data);

        if ($student->user) {
            $student->user->update([
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'] ?? null,
                'last_name' => $data['last_name'],
                'name' => trim(implode(' ', array_filter([
                    $data['first_name'], $data['middle_name'] ?? null, $data['last_name'],
                ]))),
                'admission_number' => $data['admission_number'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
            ]);

            if (! empty($data['password'])) {
                $student->user->update([
                    'password' => $data['password'],
                    'must_change_password' => false,
                ]);
            }
        }

        if (array_key_exists('subjects', $data)) {
            $this->syncSubjects($student, $data['subjects'] ?? [], $data['school_class_id']);
        }

        AuditLogService::log('student_updated', "Student {$student->full_name} ({$data['admission_number']}) updated", $student);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $name = $student->full_name;

        if ($student->user) {
            $student->user->update(['is_active' => false]);
        }

        $student->delete();

        AuditLogService::log('student_deleted', "Student {$name} removed", $student);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student removed successfully.');
    }

    protected function syncSubjects(Student $student, array $subjectIds, int $schoolClassId): void
    {
        $academicYear = $this->currentAcademicYear();

        $pivot = [];
        foreach ($subjectIds as $subjectId) {
            $pivot[$subjectId] = [
                'school_class_id' => $schoolClassId,
                'academic_year_id' => $academicYear?->id ?? null,
            ];
        }

        $student->subjects()->sync($pivot);
    }

    protected function currentAcademicYear(): ?AcademicYear
    {
        return AcademicYear::where('is_current', true)->first()
            ?? AcademicYear::latest()->first();
    }
}
