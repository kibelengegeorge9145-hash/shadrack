<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accountant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'staff_id', 'first_name', 'middle_name', 'last_name',
        'phone', 'email', 'is_active',
    ];

    protected $casts = ['is_active' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
}
