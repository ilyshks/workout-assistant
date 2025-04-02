<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'height',
        'weight',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'date_of_birth' => 'date', // Если date_of_birth хранится как DATE
        'height' => 'integer',     // Если height хранится как целое число
        'weight' => 'integer',     // Если weight хранится как целое число, или 'decimal' / 'float' при необходимости.

    ];

    // Relationships (связи с другими моделями):
    public function exerciseResults()
    {
        return $this->hasMany(UserExerciseResult::class);
    }
}
