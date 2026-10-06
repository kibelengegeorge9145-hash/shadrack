<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyParent extends Model
{
    use SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'user_id', 'first_name', 'middle_name', 'last_name',
        'phone', 'email', 'address', 'relationship',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id');
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
