<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = [
        'email', 'is_active', 'unsubscribed_at', 'ip_address',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'unsubscribed_at' => 'datetime',
    ];
}
