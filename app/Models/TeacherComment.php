<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherComment extends Model
{
    protected $fillable = ['student_id', 'teacher_user_id', 'term_id', 'body'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacherUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }
}
