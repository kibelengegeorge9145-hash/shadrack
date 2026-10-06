<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Term extends Model
{
    protected $fillable = ['academic_year_id', 'name', 'term_number', 'start_date', 'end_date', 'is_current'];

    protected $casts = ['is_current' => 'boolean', 'start_date' => 'date', 'end_date' => 'date'];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
