<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\MyParent;
use App\Models\Result;
use App\Models\ResultItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportIdorTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $student;

    protected User $otherStudent;

    protected User $parent;

    protected User $otherParent;

    protected Student $studentModel;

    protected Student $otherStudentModel;

    protected SchoolClass $class;

    protected Subject $subject;

    protected AcademicYear $year;

    protected Term $term;

    protected function setUp(): void
    {
        parent::setUp();

        // Reuse catalog records seeded into the database (tests roll back their own inserts).
        $this->year = AcademicYear::where('is_current', true)->firstOrFail();
        $this->term = Term::where('academic_year_id', $this->year->id)->orderBy('term_number')->firstOrFail();
        $this->class = SchoolClass::firstOrCreate(
            ['name' => 'Form 1', 'level' => 'O-Level'],
            ['form_number' => 1, 'label' => 'I', 'academic_year' => $this->year->name, 'is_active' => true]
        );
        $this->subject = Subject::firstOrCreate(
            ['code' => 'MAT'],
            ['name' => 'Mathematics', 'level' => 'O-Level', 'department' => 'Mathematics']
        );

        $this->admin = User::create(['email' => 'idor.admin@test.local', 'name' => 'Admin', 'first_name' => 'Admin', 'last_name' => 'Test', 'username' => 'idoradmin', 'role' => 'admin', 'password' => bcrypt('password'), 'is_active' => true]);
        $this->student = User::create(['email' => 'idor.s1@test.local', 'name' => 'Student One', 'first_name' => 'Student', 'last_name' => 'One', 'username' => 'idors1', 'role' => 'student', 'password' => bcrypt('password'), 'is_active' => true]);
        $this->otherStudent = User::create(['email' => 'idor.s2@test.local', 'name' => 'Student Two', 'first_name' => 'Student', 'last_name' => 'Two', 'username' => 'idors2', 'role' => 'student', 'password' => bcrypt('password'), 'is_active' => true]);
        $this->parent = User::create(['email' => 'idor.p1@test.local', 'name' => 'Parent One', 'first_name' => 'Parent', 'last_name' => 'One', 'username' => 'idorp1', 'role' => 'parent', 'password' => bcrypt('password'), 'is_active' => true]);
        $this->otherParent = User::create(['email' => 'idor.p2@test.local', 'name' => 'Parent Two', 'first_name' => 'Parent', 'last_name' => 'Two', 'username' => 'idorp2', 'role' => 'parent', 'password' => bcrypt('password'), 'is_active' => true]);

        $this->studentModel = Student::create(['user_id' => $this->student->id, 'admission_number' => 'TST-2026-00001', 'first_name' => 'Student', 'last_name' => 'One', 'school_class_id' => $this->class->id, 'level' => 'O-Level', 'status' => 'active', 'enrolled_on' => now(), 'parent_id' => null]);
        $this->otherStudentModel = Student::create(['user_id' => $this->otherStudent->id, 'admission_number' => 'TST-2026-00002', 'first_name' => 'Student', 'last_name' => 'Two', 'school_class_id' => $this->class->id, 'level' => 'O-Level', 'status' => 'active', 'enrolled_on' => now(), 'parent_id' => null]);

        // Link parent
        $parentModel = MyParent::create(['user_id' => $this->parent->id, 'first_name' => 'Parent', 'last_name' => 'One', 'phone' => '+255000000001', 'relationship' => 'father']);
        $this->studentModel->update(['parent_id' => $parentModel->id]);

        $otherParentModel = MyParent::create(['user_id' => $this->otherParent->id, 'first_name' => 'Parent', 'last_name' => 'Two', 'phone' => '+255000000002', 'relationship' => 'mother']);
        $this->otherStudentModel->update(['parent_id' => $otherParentModel->id]);

        // Create results + items for student 1 so report generation works
        $result = Result::create(['student_id' => $this->studentModel->id, 'school_class_id' => $this->class->id, 'term_id' => $this->term->id, 'academic_year_id' => $this->year->id, 'status' => 'published']);
        ResultItem::create(['result_id' => $result->id, 'subject_id' => $this->subject->id, 'marks' => 75]);
    }

    public function test_student_cannot_access_other_students_pdf(): void
    {
        // Student 2 tries to access student 1's report
        $response = $this->actingAs($this->otherStudent)
            ->get(route('student.reports.pdf', [$this->studentModel, $this->term, $this->year]));

        $response->assertStatus(403);
    }

    public function test_student_can_access_own_report_pdf(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('student.reports.pdf', [$this->studentModel, $this->term, $this->year]));

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_student_cannot_view_other_students_report_card(): void
    {
        $response = $this->actingAs($this->otherStudent)
            ->get(route('student.reports.show', [$this->studentModel, $this->term, $this->year]));

        $response->assertStatus(403);
    }

    public function test_parent_cannot_access_other_parents_child_report(): void
    {
        $response = $this->actingAs($this->otherParent)
            ->get(route('parent.reports.pdf', [$this->studentModel, $this->term, $this->year]));

        $response->assertStatus(403);
    }

    public function test_parent_can_access_own_child_report(): void
    {
        $response = $this->actingAs($this->parent)
            ->get(route('parent.reports.pdf', [$this->studentModel, $this->term, $this->year]));

        $response->assertStatus(200)
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_accountant_cannot_access_admin_report_center(): void
    {
        $accountant = User::create(['email' => 'acc@test.com', 'name' => 'Acc', 'first_name' => 'Acc', 'last_name' => 'Test', 'username' => 'acctst', 'role' => 'accountant', 'password' => bcrypt('password'), 'is_active' => true]);

        $response = $this->actingAs($accountant)
            ->get(route('admin.reports.academic'));

        $response->assertStatus(403);
    }

    public function test_accountant_cannot_generate_report_pdf(): void
    {
        $accountant = User::create(['email' => 'acc@test.com', 'name' => 'Acc', 'first_name' => 'Acc', 'last_name' => 'Test', 'username' => 'acctst', 'role' => 'accountant', 'password' => bcrypt('password'), 'is_active' => true]);

        $response = $this->actingAs($accountant)->get(route('admin.reports.academic.pdf', [
            'report_type' => 'student',
            'academic_year_id' => $this->year->id,
            'term_id' => $this->term->id,
            'student_id' => $this->studentModel->id,
        ]));

        $response->assertStatus(403);
    }

    public function test_teacher_can_access_report_center(): void
    {
        $teacher = User::create(['email' => 'tch@test.com', 'name' => 'Teacher', 'first_name' => 'Teacher', 'last_name' => 'Test', 'username' => 'tchtst', 'role' => 'teacher', 'password' => bcrypt('password'), 'is_active' => true]);

        $response = $this->actingAs($teacher)
            ->get(route('teacher.reports.academic'));

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_admin_report_center(): void
    {
        $response = $this->actingAs($this->student)
            ->get(route('admin.reports.academic'));

        $response->assertStatus(403);
    }

    public function test_admin_can_dispatch_queued_report(): void
    {
        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.reports.queue'), [
                'report_type' => 'student',
                'academic_year_id' => $this->year->id,
                'term_id' => $this->term->id,
                'student_id' => $this->studentModel->id,
                'format' => 'pdf',
            ]);

        $response->assertStatus(202)
            ->assertJsonStructure(['message']);
    }

    public function test_accountant_cannot_dispatch_queued_report(): void
    {
        $accountant = User::create(['email' => 'acc2@test.com', 'name' => 'Acc2', 'first_name' => 'Acc', 'last_name' => 'Two', 'username' => 'acc2tst', 'role' => 'accountant', 'password' => bcrypt('password'), 'is_active' => true]);

        $response = $this->actingAs($accountant)
            ->postJson(route('admin.reports.queue'), [
                'report_type' => 'class',
                'academic_year_id' => $this->year->id,
                'term_id' => $this->term->id,
                'school_class_id' => $this->class->id,
                'format' => 'pdf',
            ]);

        $response->assertStatus(403);
    }
}
