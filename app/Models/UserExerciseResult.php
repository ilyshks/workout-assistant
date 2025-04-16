<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class UserExerciseResult extends Model
{
    use HasFactory;
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
