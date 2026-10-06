<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher', 'accountant');
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            return $user->teacher?->classes()->where('school_classes.id', $student->school_class_id)->exists() ||
                $user->teacher?->subjects()->where('subjects.id', $student->school_class_id)->exists() ||
                $user->teacher?->id === $student->schoolClass?->class_teacher_id;
        }

        if ($user->isParent()) {
            return $student->parent_id === $user->parent?->id;
        }

        if ($user->isStudent()) {
            return $student->user_id === $user->id;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isAdmin();
    }
}
