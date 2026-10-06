<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeStructure extends Model
{
    public const CATEGORIES = [
        'Tuition', 'Accommodation', 'Meals', 'Examination', 'Transport',
        'Uniform', 'Laboratory', 'Computer', 'Sports', 'Other',
    ];

    protected $fillable = [
        'name', 'category', 'amount', 'level', 'school_class_id',
        'academic_year_id', 'description', 'is_active',
    ];

    protected $casts = ['amount' => 'decimal:2', 'is_active' => 'boolean'];

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }
}
