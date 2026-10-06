<?php

namespace App\Policies;

use App\Models\Result;
use App\Models\User;

class ResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin', 'teacher', 'accountant');
    }

    public function view(User $user, Result $result): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Students / parents see only published results for their own student.
        if ($result->isPublished()) {
            if ($user->isStudent() && $result->student_id === $user->student?->id) {
                return true;
            }
            if ($user->isParent() && $result->student && $result->student->parent_id === $user->parent?->id) {
                return true;
            }
        }

        if ($user->isTeacher()) {
            // Teacher can view results for their assigned classes/subjects.
            return $user->teacher?->classes()->where('school_classes.id', $result->school_class_id)->exists() ||
                $result->items()->where('teacher_id', $user->teacher->id)->exists();
        }

        return false;
    }

    // Teacher can create/manage draft + submit for their assigned students.
    public function manage(User $user, Result $result): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeacher()) {
            if ($result->isPublished()) {
                return false;
            }

            return $user->teacher?->classes()->where('school_classes.id', $result->school_class_id)->exists();
        }

        return false;
    }

    public function submit(User $user, Result $result): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isTeacher() && ! $result->isPublished()) {
            return $user->teacher?->classes()->where('school_classes.id', $result->school_class_id)->exists();
        }

        return false;
    }

    // Only the admin can publish.
    public function publish(User $user, Result $result): bool
    {
        return $user->isAdmin() && ! $result->isPublished();
    }

    public function delete(User $user, Result $result): bool
    {
        return $user->isAdmin() && ! $result->isPublished();
    }
}
