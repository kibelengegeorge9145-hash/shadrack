<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject', 'message',
        'is_read', 'ip_address',
    ];

    protected $casts = ['is_read' => 'boolean'];

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}
