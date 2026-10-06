<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentComment extends Model
{
    protected $fillable = ['student_id', 'parent_user_id', 'term_id', 'body'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_user_id');
    }
}
