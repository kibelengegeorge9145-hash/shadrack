<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admission extends Model
{
    use SoftDeletes;

    public const STATUSES = ['pending', 'under_review', 'accepted', 'rejected'];

    protected $fillable = [
        'application_number', 'first_name', 'middle_name', 'last_name',
        'date_of_birth', 'gender', 'current_school', 'applying_class',
        'level', 'parent_name', 'parent_phone', 'parent_email', 'status', 'notes',
    ];

    protected $casts = ['date_of_birth' => 'date'];

    public function getFullNameAttribute(): string
    {
        return trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name])));
    }
}
