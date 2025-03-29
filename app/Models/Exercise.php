<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $primaryKey = 'exercise_id';
    protected $fillable = [
        'muscle_group', 'exercise_name', 'tutorial'
    ];
}
