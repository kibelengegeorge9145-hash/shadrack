<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'admission_number', 'first_name', 'middle_name', 'last_name',
        'date_of_birth', 'gender', 'school_class_id', 'level', 'parent_id',
        'phone', 'email', 'photo', 'status', 'enrolled_on',
    ];

    protected $casts = ['date_of_birth' => 'date', 'enrolled_on' => 'date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MyParent::class, 'parent_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class, 'student_subjects')
            ->withPivot('school_class_id', 'academic_year_id');
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }

    public function studentFees(): HasMany
    {
        return $this->hasMany(StudentFee::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name])));
    }

    public function getInitialsAttribute(): string
    {
        return collect([$this->first_name, $this->last_name])
            ->filter()
            ->map(fn ($p) => mb_strtoupper(mb_substr((string) $p, 0, 1)))
            ->take(2)
            ->implode('');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
