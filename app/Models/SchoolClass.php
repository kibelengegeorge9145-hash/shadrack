<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'school_classes';

    protected $fillable = [
        'name', 'level', 'form_number', 'label', 'academic_year',
        'class_teacher_id', 'description', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(Teacher::class, 'class_teacher_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'teacher_subjects', 'school_class_id', 'subject_id');
    }

    public function timetable(): HasMany
    {
        return $this->hasMany(Timetable::class);
    }

    public function getLevelLabelAttribute(): string
    {
        return $this->level;
    }
}
