<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    public const STATUSES = ['present', 'absent', 'late', 'excused'];

    protected $table = 'attendance';

    protected $fillable = [
        'student_id', 'school_class_id', 'academic_year_id', 'term_id',
        'date', 'status', 'remarks', 'recorded_by',
    ];

    protected $casts = ['date' => 'date'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
