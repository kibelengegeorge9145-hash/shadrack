<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = ['code', 'name', 'level', 'department', 'is_optional', 'is_active'];

    protected $casts = ['is_optional' => 'boolean', 'is_active' => 'boolean'];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'student_subjects')
            ->withPivot('school_class_id', 'academic_year_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects')
            ->withPivot('school_class_id');
    }

    public function resultItems(): HasMany
    {
        return $this->hasMany(ResultItem::class);
    }
}
