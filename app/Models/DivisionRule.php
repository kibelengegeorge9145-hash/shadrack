<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DivisionRule extends Model
{
    protected $fillable = ['level', 'division', 'min_points', 'max_points', 'sort_order'];

    public static function forLevel(string $level)
    {
        return static::where('level', $level)->orderBy('sort_order')->get();
    }
}
