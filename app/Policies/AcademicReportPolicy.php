<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Term;
use App\Models\User;

class AcademicReportPolicy
{
    /**
     * Whether the user can open the Academic Report Center / generate reports.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher', 'accountant');
    }

    /**
     * Whether a user can generate a student report card for a given student.
     */
    public function viewStudent(User $user, Student $student): bool
    {
        if ($user->isAdmin() || $user->isAccountant()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $this->teacherHasAccess($user, $student->school_class_id);
        }

        if ($user->isStudent()) {
            return $user->student?->id === $student->id;
        }

        if ($user->isParent()) {
            return $student->parent_id === $user->parent?->id;
        }

        return false;
    }

    /**
     * Whether a user can generate a class-level / subject / term report for a given class.
     */
    public function viewClass(User $user, ?SchoolClass $schoolClass = null): bool
    {
        if ($user->isAdmin() || $user->isAccountant()) {
            return true;
        }

        if ($user->isTeacher()) {
            if (! $schoolClass) {
                return true; // level/term/school-wide reports across assigned lessons
            }

            return $this->teacherHasAccess($user, $schoolClass->id);
        }

        return false;
    }

    /**
     * Whether a user may view a single (published) Result.
     */
    public function viewResult(User $user, Result $result): bool
    {
        if ($user->isAdmin() || $user->isAccountant()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $this->teacherHasAccess($user, $result->school_class_id);
        }

        // Students / parents only ever see published results, and never another person's.
        if (! $result->isPublished()) {
            return false;
        }

        if ($user->isStudent()) {
            return $result->student_id === $user->student?->id;
        }

        if ($user->isParent()) {
            return $result->student && $result->student->parent_id === $user->parent?->id;
        }

        return false;
    }

    public function viewSubject(User $user, Subject $subject, ?SchoolClass $schoolClass = null): bool
    {
        if ($user->isAdmin() || $user->isAccountant()) {
            return true;
        }

        if ($user->isTeacher()) {
            if ($schoolClass) {
                return $this->teacherHasAccess($user, $schoolClass->id);
            }

            return $user->teacher?->subjects()->where('subjects.id', $subject->id)->exists();
        }

        return false;
    }

    public function viewTerm(User $user, Term $term): bool
    {
        return $user->hasRole('admin', 'teacher', 'accountant');
    }

    public function viewYear(User $user, AcademicYear $year): bool
    {
        return $user->hasRole('admin', 'teacher', 'accountant');
    }

    protected function teacherHasAccess(User $user, ?int $classId): bool
    {
        if (! $classId) {
            return false;
        }

        return $user->teacher?->classes()->where('school_classes.id', $classId)->exists()
            || $user->teacher?->subjects()->wherePivot('school_class_id', $classId)->exists();
    }
}
