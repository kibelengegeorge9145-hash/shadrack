<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubjectCombination extends Model
{
    protected $fillable = ['code', 'name', 'level', 'description'];

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(Subject::class);
    }
}
