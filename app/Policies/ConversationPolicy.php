<?php

namespace App\Policies;

use App\Models\Conversation;
use App\Models\Student;
use App\Models\User;

class ConversationPolicy
{
    public function view(User $user, Conversation $conversation): bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        if ($user->isParent()) {
            return $conversation->parent_user_id === $user->id;
        }
        if ($user->isTeacher()) {
            return $conversation->teacher_user_id === $user->id;
        }

        return false;
    }

    public function reply(User $user, Conversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }

    public function create(User $user, ?int $studentId = null): bool
    {
        // Parent can only message teachers of their own children.
        if ($user->isParent() && $studentId) {
            $student = Student::find($studentId);

            return $student && $student->parent_id === $user->parent?->id;
        }

        return $user->hasRole('admin', 'teacher', 'parent');
    }
}
