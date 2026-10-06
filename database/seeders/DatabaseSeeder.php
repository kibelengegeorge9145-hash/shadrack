<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Accountant;
use App\Models\Admission;
use App\Models\Announcement;
use App\Models\DivisionRule;
use App\Models\Event;
use App\Models\FeeStructure;
use App\Models\Gallery;
use App\Models\GradingRule;
use App\Models\MyParent;
use App\Models\News;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Term;
use App\Models\User;
use App\Services\AuthService;
use App\Services\FeeService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $auth = new AuthService;

        $this->seedRoles();
        [$year, $term] = $this->seedAcademicCalendar();
        $classes = $this->seedClasses();
        $subjects = $this->seedSubjects();

        $this->seedGradingAndDivisions();

        $adminUser = $this->seedAdmin();
        $accountantUser = $this->seedAccountant();

        $teacherUser = $this->seedTeacher($classes, $subjects);
        $studentUser = $this->seedStudent($classes, $subjects, $year);
        $parentUser = $this->seedParent($studentUser);

        $this->seedFees($classes, $year, $term, $studentUser);
        $this->seedContent($adminUser);
        $this->seedGallery();

        $this->call(ResultSeeder::class);
    }

    protected function seedRoles(): void
    {
        $roles = [
            'admin' => 'School Administrator',
            'accountant' => 'Accounts & Finance Officer',
            'teacher' => 'Teaching Staff',
            'parent' => 'Parent / Guardian',
            'student' => 'Student',
        ];

        foreach ($roles as $slug => $description) {
            Role::updateOrCreate(['slug' => $slug], [
                'name' => ucfirst($slug),
                'description' => $description,
            ]);
        }
    }

    /** @return array{0: AcademicYear, 1: Term} */
    protected function seedAcademicCalendar(): array
    {
        $year = AcademicYear::firstOrCreate(
            ['name' => date('Y').'/'.(date('Y') + 1)],
            [
                'start_date' => now()->startOfYear(),
                'end_date' => now()->endOfYear(),
                'is_current' => true,
            ]
        );

        $term = Term::firstOrCreate(
            ['academic_year_id' => $year->id, 'term_number' => 1],
            [
                'name' => 'Term 1',
                'start_date' => now(),
                'end_date' => now()->addMonths(4),
                'is_current' => true,
            ]
        );

        Term::firstOrCreate(
            ['academic_year_id' => $year->id, 'term_number' => 2],
            ['name' => 'Term 2', 'is_current' => false]
        );
        Term::firstOrCreate(
            ['academic_year_id' => $year->id, 'term_number' => 3],
            ['name' => 'Term 3', 'is_current' => false]
        );

        return [$year, $term];
    }

    /** @return Collection<int, SchoolClass> */
    protected function seedClasses()
    {
        $definitions = [
            ['name' => 'Form 1', 'level' => 'O-Level', 'form_number' => 1, 'label' => 'I'],
            ['name' => 'Form 2', 'level' => 'O-Level', 'form_number' => 2, 'label' => 'II'],
            ['name' => 'Form 3', 'level' => 'O-Level', 'form_number' => 3, 'label' => 'III'],
            ['name' => 'Form 4', 'level' => 'O-Level', 'form_number' => 4, 'label' => 'IV'],
            ['name' => 'Form 5', 'level' => 'A-Level', 'form_number' => 5, 'label' => 'V'],
            ['name' => 'Form 6', 'level' => 'A-Level', 'form_number' => 6, 'label' => 'VI'],
        ];

        $classes = collect();
        foreach ($definitions as $def) {
            $classes->push(SchoolClass::firstOrCreate(
                ['name' => $def['name'], 'level' => $def['level']],
                [...$def, 'academic_year' => date('Y').'/'.(date('Y') + 1), 'is_active' => true]
            ));
        }

        return $classes;
    }

    /** @return Collection<int, Subject> */
    protected function seedSubjects()
    {
        $oLevel = [
            ['MAT', 'Mathematics', 'O-Level', 'Mathematics'],
            ['ENG', 'English Language', 'O-Level', 'Languages'],
            ['KIS', 'Kiswahili', 'O-Level', 'Languages'],
            ['PHY', 'Physics', 'O-Level', 'Sciences'],
            ['CHE', 'Chemistry', 'O-Level', 'Sciences'],
            ['BIO', 'Biology', 'O-Level', 'Sciences'],
            ['GEO', 'Geography', 'O-Level', 'Humanities'],
            ['HIS', 'History', 'O-Level', 'Humanities'],
            ['CIV', 'Civics', 'O-Level', 'Humanities'],
            ['BKT', 'Book Keeping', 'O-Level', 'Business'],
        ];

        $aLevel = [
            ['PCM', 'Physics, Chemistry & Mathematics', 'A-Level', 'Sciences'],
            ['CBG', 'Chemistry, Biology & Geography', 'A-Level', 'Sciences'],
            ['HGL', 'History, Geography & Literature', 'A-Level', 'Humanities'],
            ['HGE', 'History, Geography & Economics', 'A-Level', 'Humanities'],
        ];

        $subjects = collect();
        foreach (array_merge($oLevel, $aLevel) as $s) {
            $subjects->push(Subject::firstOrCreate(
                ['code' => $s[0]],
                ['name' => $s[1], 'level' => $s[2], 'department' => $s[3]]
            ));
        }

        return $subjects;
    }

    protected function seedGradingAndDivisions(): void
    {
        $o = [
            [80, 100, 'A', 1, true],
            [70, 79, 'B', 2, true],
            [60, 69, 'C', 3, true],
            [50, 59, 'D', 4, true],
            [40, 49, 'E', 5, true],
            [0, 39, 'F', 6, false],
        ];
        foreach ($o as $r) {
            GradingRule::updateOrCreate(
                ['level' => 'O-Level', 'min_mark' => $r[0], 'max_mark' => $r[1]],
                ['grade' => $r[2], 'points' => $r[3], 'is_pass' => $r[4]]
            );
        }

        // A-Level: A=1 to E=5 (out of 6 optional) — simplified
        $a = [
            [80, 100, 'A', 1, true],
            [70, 79, 'B', 2, true],
            [60, 69, 'C', 3, true],
            [50, 59, 'D', 4, true],
            [40, 49, 'E', 5, true],
            [0, 39, 'F', 6, false],
        ];
        foreach ($a as $r) {
            GradingRule::updateOrCreate(
                ['level' => 'A-Level', 'min_mark' => $r[0], 'max_mark' => $r[1]],
                ['grade' => $r[2], 'points' => $r[3], 'is_pass' => $r[4]]
            );
        }

        $divisions = [
            ['O-Level', 'Division I', 1, 7, 1],
            ['O-Level', 'Division II', 8, 21, 2],
            ['O-Level', 'Division III', 22, 30, 3],
            ['O-Level', 'Division IV', 31, 42, 4],
            ['O-Level', 'FAIL', 43, 99, 5],
            ['A-Level', 'Division I', 1, 3, 1],
            ['A-Level', 'Division II', 4, 6, 2],
            ['A-Level', 'Division III', 7, 9, 3],
            ['A-Level', 'Division IV', 10, 15, 4],
            ['A-Level', 'FAIL', 16, 99, 5],
        ];
        foreach ($divisions as $d) {
            DivisionRule::updateOrCreate(
                ['level' => $d[0], 'division' => $d[1]],
                ['min_points' => $d[2], 'max_points' => $d[3], 'sort_order' => $d[4]]
            );
        }
    }

    protected function seedAdmin(): User
    {
        return User::firstOrCreate(
            ['email' => 'admin@ebenezer.edu.tz'],
            [
                'name' => 'School Administrator',
                'first_name' => 'School',
                'last_name' => 'Administrator',
                'username' => 'admin',
                'phone' => '+255700000001',
                'role' => 'admin',
                'password' => Hash::make('Password@123'),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );
    }

    protected function seedAccountant(): User
    {
        $user = User::firstOrCreate(
            ['email' => 'accountant@ebenezer.edu.tz'],
            [
                'name' => 'Grace Accountant',
                'first_name' => 'Grace',
                'last_name' => 'Accountant',
                'username' => 'accountant',
                'phone' => '+255700000002',
                'role' => 'accountant',
                'password' => Hash::make('Password@123'),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        Accountant::firstOrCreate(
            ['user_id' => $user->id],
            [
                'staff_id' => 'EBA0001',
                'first_name' => 'Grace',
                'last_name' => 'Accountant',
                'phone' => '+255700000002',
                'email' => 'accountant@ebenezer.edu.tz',
                'is_active' => true,
            ]
        );

        return $user;
    }

    protected function seedTeacher($classes, $subjects): User
    {
        $user = User::firstOrCreate(
            ['email' => 'teacher@ebenezer.edu.tz'],
            [
                'name' => 'John Teacher',
                'first_name' => 'John',
                'last_name' => 'Teacher',
                'username' => 'teacher',
                'phone' => '+255700000003',
                'role' => 'teacher',
                'password' => Hash::make('Password@123'),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        $teacher = Teacher::firstOrCreate(
            ['user_id' => $user->id],
            [
                'staff_id' => 'EBT0001',
                'first_name' => 'John',
                'last_name' => 'Teacher',
                'phone' => '+255700000003',
                'email' => 'teacher@ebenezer.edu.tz',
                'gender' => 'male',
                'department' => 'Sciences',
                'qualification' => 'BSc Education',
                'is_active' => true,
            ]
        );

        $teacher->classes()->syncWithoutDetaching($classes->slice(0, 2)->pluck('id'));
        $teacher->subjects()->syncWithoutDetaching(
            $subjects->slice(0, 3)->pluck('id')->mapWithKeys(fn ($id) => [$id => ['school_class_id' => $classes->first()->id]])
        );

        return $user;
    }

    protected function seedStudent($classes, $subjects, $year): User
    {
        $user = User::firstOrCreate(
            ['email' => 'student@ebenezer.edu.tz'],
            [
                'name' => 'Ali Student',
                'first_name' => 'Ali',
                'last_name' => 'Student',
                'username' => 'student',
                'phone' => '+255700000004',
                'role' => 'student',
                'password' => Hash::make('Password@123'),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        $class = $classes->firstWhere('name', 'Form 1');
        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'admission_number' => 'EBSS-'.date('Y').'-00001',
                'first_name' => 'Ali',
                'last_name' => 'Student',
                'gender' => 'male',
                'school_class_id' => $class->id,
                'level' => 'O-Level',
                'status' => 'active',
                'enrolled_on' => now(),
            ]
        );

        $student->subjects()->syncWithoutDetaching(
            $subjects->slice(0, 4)->pluck('id')->mapWithKeys(function ($id) use ($class, $year) {
                return [$id => ['school_class_id' => $class->id, 'academic_year_id' => $year->id]];
            })
        );

        $user->forceFill(['admission_number' => $student->admission_number])->save();

        // A second demo student without a user account
        Student::firstOrCreate(
            ['admission_number' => 'EBSS-'.date('Y').'-00002'],
            [
                'first_name' => 'Neema',
                'last_name' => 'Student',
                'gender' => 'female',
                'school_class_id' => $class->id,
                'level' => 'O-Level',
                'status' => 'active',
                'enrolled_on' => now(),
            ]
        );

        return $user;
    }

    protected function seedParent($studentUser): User
    {
        $user = User::firstOrCreate(
            ['email' => 'parent@ebenezer.edu.tz'],
            [
                'name' => 'Maria Parent',
                'first_name' => 'Maria',
                'last_name' => 'Parent',
                'username' => 'parent',
                'phone' => '+255700000005',
                'role' => 'parent',
                'password' => Hash::make('Password@123'),
                'must_change_password' => false,
                'is_active' => true,
            ]
        );

        $parent = MyParent::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => 'Maria',
                'last_name' => 'Parent',
                'phone' => '+255700000005',
                'email' => 'parent@ebenezer.edu.tz',
                'relationship' => 'mother',
            ]
        );

        $student = Student::where('user_id', $studentUser->id)->first();
        if ($student) {
            $student->forceFill(['parent_id' => $parent->id])->save();
        }

        return $user;
    }

    protected function seedFees($classes, $year, $term, $studentUser): void
    {
        $oClass = $classes->firstWhere('name', 'Form 1');

        $tuition = FeeStructure::firstOrCreate(
            ['name' => 'Tuition - O-Level', 'category' => 'Tuition'],
            ['amount' => 350000, 'level' => 'O-Level', 'school_class_id' => $oClass->id, 'academic_year_id' => $year->id, 'is_active' => true]
        );
        $exams = FeeStructure::firstOrCreate(
            ['name' => 'Examination Fee - O-Level', 'category' => 'Examination'],
            ['amount' => 50000, 'level' => 'O-Level', 'school_class_id' => $oClass->id, 'academic_year_id' => $year->id, 'is_active' => true]
        );

        $student = Student::where('user_id', $studentUser->id)->first();
        if ($student) {
            app(FeeService::class)->syncStudentFees($student, $year->id, $term->id);
        }
    }

    protected function seedContent($adminUser): void
    {
        News::firstOrCreate(
            ['slug' => 'welcome-to-the-new-academic-year'],
            [
                'title' => 'Welcome to the New Academic Year',
                'excerpt' => 'We are excited to welcome both new and returning students.',
                'body' => 'Ebenezer Secondary School is delighted to welcome all students for the new academic year. We look forward to another year of excellence, discipline and academic achievement.',
                'author_id' => $adminUser->id,
                'is_published' => true,
                'published_at' => now(),
            ]
        );

        Event::firstOrCreate(
            ['title' => 'Parents Day'],
            [
                'description' => 'Annual parents meeting to review student progress and school development.',
                'location' => 'School Assembly Hall',
                'starts_at' => now()->addWeeks(2),
                'ends_at' => now()->addWeeks(2)->addHours(4),
                'is_published' => true,
            ]
        );

        Announcement::firstOrCreate(
            ['title' => 'Term 1 begins'],
            [
                'body' => 'Term 1 has officially begun. All students should report to class on time and complete registration.',
                'author_id' => $adminUser->id,
                'audience' => 'everyone',
                'is_active' => true,
                'published_at' => now(),
            ]
        );

        Admission::firstOrCreate(
            ['application_number' => 'ADM-'.date('Y').'-00001'],
            [
                'first_name' => 'Peter',
                'last_name' => 'Applicant',
                'gender' => 'male',
                'applying_class' => 'Form 1',
                'level' => 'O-Level',
                'parent_name' => 'James Applicant',
                'parent_phone' => '+255700000099',
                'status' => 'pending',
            ]
        );
    }

    protected function seedGallery(): void
    {
        $photos = [
            ['Classrooms', 'Modern learning spaces', 1],
            ['Science Laboratory', 'Practical learning in action', 2],
            ['Library', 'A quiet corner for study', 3],
            ['School Library', 'Reading is the key to success', 4],
            ['Sports Day', 'Team spirit on the field', 5],
            ['Computer Laboratory', 'Digital skills for the future', 6],
            ['Graduation Ceremony', 'Celebrating our achievers', 7],
            ['School Campus', 'Our beautiful grounds', 8],
        ];

        foreach ($photos as $index => $p) {
            Gallery::firstOrCreate(
                ['image' => '/storage/gallery/gallery_'.($index + 1).'.jpg'],
                ['title' => $p[0], 'caption' => $p[1]]
            );
        }
    }
}
