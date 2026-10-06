<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    public const AUDIENCES = ['everyone', 'students', 'parents', 'teachers', 'o_level', 'a_level', 'class'];

    protected $fillable = ['title', 'body', 'author_id', 'audience', 'school_class_id', 'is_active', 'published_at'];

    protected $casts = ['is_active' => 'boolean', 'published_at' => 'datetime'];

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }
}
