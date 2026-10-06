<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradingRule extends Model
{
    protected $fillable = ['level', 'min_mark', 'max_mark', 'grade', 'points', 'is_pass'];

    protected $casts = ['is_pass' => 'boolean'];

    public static function forLevel(string $level)
    {
        return static::where('level', $level)->orderBy('min_mark')->get();
    }
}
