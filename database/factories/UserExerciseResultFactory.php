<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserExerciseResult>
 */
class UserExerciseResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'exercise_id' => Exercise::inRandomOrder()->first()->id,

            'record_weight' => $this->faker->numberBetween(20, 150),
            'record_repeats' => $this->faker->numberBetween(5, 20),

            'last_weight' => $this->faker->numberBetween(20, 150),
            'last_repeats' => $this->faker->numberBetween(5, 20),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
