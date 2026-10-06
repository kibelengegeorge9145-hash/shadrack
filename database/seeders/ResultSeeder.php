<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Result;
use App\Models\ResultItem;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\GradingService;
use Illuminate\Database\Seeder;

class ResultSeeder extends Seeder
{
    /**
     * Seed realistic published results for demo students so the Academic
     * Report Center has real data for every report type.
     *
     * Idempotent: existing demo results for the current year are replaced.
     */
    public function run(): void
    {
        $grading = new GradingService;

        $year = AcademicYear::where('is_current', true)->first() ?? AcademicYear::first();
        if (! $year) {
            return;
        }

        // Ensure a small but realistic student population per level.
        $this->ensureStudents($year);

        $terms = $year->terms()->orderBy('term_number')->get();
        $teacher = Teacher::first();
        $admin = User::where('role', 'admin')->first();

        // Remove any previous seeded results for the current year's students
        // so re-running the seeder stays idempotent and deterministic.
        $demoStudents = Student::with('subjects', 'schoolClass')->get();
        Result::where('academic_year_id', $year->id)
            ->whereIn('student_id', $demoStudents->pluck('id'))
            ->delete();

        foreach ($demoStudents as $student) {
            if (! $student->schoolClass || ! $student->level) {
                continue;
            }
            foreach ($terms as $term) {
                $this->seedResultFor($student, $term, $year, $teacher, $admin, $grading);
            }
        }

        // Recalculate positions per class + term.
        foreach (SchoolClass::all() as $class) {
            foreach ($terms as $term) {
                $classResults = Result::with('items')
                    ->where('school_class_id', $class->id)
                    ->where('term_id', $term->id)
                    ->where('academic_year_id', $year->id)
                    ->get()
                    ->filter(fn ($r) => $r->items->isNotEmpty());
                if ($classResults->isNotEmpty()) {
                    $grading->assignPositions($classResults);
                }
            }
        }
    }

    protected function ensureStudents(AcademicYear $year): void
    {
        $oClass = SchoolClass::where('name', 'Form 1')->first();
        $aClass = SchoolClass::where('name', 'Form 5')->first();

        $oSubjects = Subject::where('level', 'O-Level')->orderBy('id')->take(4)->get();
        $aSubjects = Subject::where('level', 'A-Level')->orderBy('id')->take(2)->get();

        if ($oClass) {
            foreach (Student::where('school_class_id', $oClass->id)->get() as $student) {
                if ($student->subjects()->count() === 0) {
                    $student->subjects()->syncWithoutDetaching(
                        $oSubjects->mapWithKeys(fn ($s) => [$s->id => ['school_class_id' => $oClass->id, 'academic_year_id' => $year->id]])
                    );
                }
            }
        }

        $aLevel = [
            ['EBSS-'.date('Y').'-00003', 'Juma', 'Omar', 'male'],
            ['EBSS-'.date('Y').'-00004', 'Asha', 'Hassan', 'female'],
        ];

        foreach ($aLevel as [$adm, $first, $last, $gender]) {
            if (! Student::where('admission_number', $adm)->exists() && $aClass) {
                Student::create([
                    'admission_number' => $adm,
                    'first_name' => $first,
                    'last_name' => $last,
                    'gender' => $gender,
                    'school_class_id' => $aClass->id,
                    'level' => 'A-Level',
                    'status' => 'active',
                    'enrolled_on' => now(),
                ]);
            }
        }

        if ($aClass) {
            foreach (Student::where('school_class_id', $aClass->id)->get() as $student) {
                if ($student->subjects()->count() === 0) {
                    $student->subjects()->syncWithoutDetaching(
                        $aSubjects->mapWithKeys(fn ($s) => [$s->id => ['school_class_id' => $aClass->id, 'academic_year_id' => $year->id]])
                    );
                }
            }
        }
    }

    protected function seedResultFor(Student $student, $term, AcademicYear $year, ?Teacher $teacher, ?User $admin, GradingService $grading): void
    {
        $class = $student->schoolClass;
        $subjects = $student->subjects->where('level', $student->level)->values();

        if ($subjects->isEmpty()) {
            return;
        }

        $passBase = [
            'Ali' => 82,
            'Neema' => 66,
            'Juma' => 74,
            'Asha' => 58,
        ];

        $base = $passBase[$student->first_name] ?? 70;

        $result = Result::create([
            'student_id' => $student->id,
            'school_class_id' => $class->id,
            'academic_year_id' => $year->id,
            'term_id' => $term->id,
            'level' => $student->level,
            'status' => Result::STATUS_PUBLISHED,
            'submitted_by' => $admin?->id,
            'published_by' => $admin?->id,
            'published_at' => now(),
            'teacher_comment' => $base >= 70
                ? 'Consistent and diligent work. Keep up the excellent standard.'
                : 'Good effort, though more focus is required in the weaker subjects.',
            'head_comment' => 'Promoted on academic merit. Parents are encouraged to monitor progress.',
        ]);

        foreach ($subjects as $subject) {
            $jitter = ((int) $subject->id * 7 + (int) $term->term_number * 13) % 14 - 7;
            $mark = min(98, max(30, $base + $jitter));
            $graded = $grading->grade($mark, $student->level);

            ResultItem::create([
                'result_id' => $result->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher?->id,
                'marks' => $mark,
                'grade' => $graded['grade'],
                'points' => $graded['points'],
                'teacher_comment' => $graded['is_pass'] ? 'Satisfactory performance.' : 'Needs improvement in this subject.',
            ]);
        }

        $result->load('items');
        $result->fill($grading->summarize($result))->save();
    }
}
