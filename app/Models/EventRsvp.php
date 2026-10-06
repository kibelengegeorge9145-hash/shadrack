<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventRsvp extends Model
{
    protected $fillable = [
        'event_id', 'name', 'email', 'guests', 'ip_address',
    ];

    protected $casts = ['guests' => 'integer'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
