<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class UserExerciseResult extends Model
{
    protected $table = 'user_exercise_results';

    protected $fillable = [
        'user_id', 'exercise_id', 'record_weight', 'record_repeats', 'last_weight', 'last_repeats'
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id', 'exercise_id');
    }

}
