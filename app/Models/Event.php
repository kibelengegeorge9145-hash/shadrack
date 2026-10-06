<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = ['title', 'description', 'location', 'starts_at', 'ends_at', 'image', 'is_published'];

    protected $casts = ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_published' => 'boolean'];

    public function rsvps(): HasMany
    {
        return $this->hasMany(EventRsvp::class);
    }

    public function comments(): HasMany
    {
        return $this->morphMany(Comment::class, 'commentable')->approved();
    }
}
