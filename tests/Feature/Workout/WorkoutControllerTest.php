<?php

namespace Tests\Feature\Workout;

use App\Models\Exercise;
use App\Models\User;
use App\Models\UserExerciseResult;
use Database\Seeders\ExercisesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class WorkoutControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private $exercises;
    private array $validWorkoutData;


    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ExercisesTableSeeder::class]);
        $this->user = User::factory()->create();
        $this->exercises = Exercise::take(3)->get();

        $this->validWorkoutData = [
            'exercises' => [
                [
                    'exercise_name' => $this->exercises->get(0)->exercise_name,
                    'weight' => 60,
                    'reps' => 8
                ],
                [
                    'exercise_name' => $this->exercises->get(1)->exercise_name,
                    'weight' => 70,
                    'reps' => 10
                ],
            ]];
    }

    public function test_returns_401_unauthorized_if_user_is_not_logged_in()
    {
        $response = $this->postJson('/api/v1/workouts', []);
        $response->assertUnauthorized();
    }

    public function test_returns_validation_errors_if_input_data_is_invalid()
    {
        $invalidWorkoutData = ['exercises' => 'notAnArray'];
        $response = $this->actingAs($this->user)->postJson('/api/v1/workouts', $invalidWorkoutData);

        $response->assertStatus(422)
        ->assertJsonPath('errors.0.code', 'validation_error')
            ->assertJsonPath('data', null);

        $invalidWorkoutData = ['exercises' => [[ 'exercise_name' => null]]];

        $response = $this->actingAs($this->user)->postJson('/api/v1/workouts', $invalidWorkoutData);

        $response->assertStatus(422)
        ->assertJsonPath('errors.0.code', 'validation_error')
            ->assertJsonPath('data', null);
    }

    public function test_ignores_non_existent_exercises_and_saves_workout()
    {
        $workoutDataWithNonExistent = $this->validWorkoutData;
        $workoutDataWithNonExistent['exercises'][] = [
            'exercise_name' => 'NonExistentExercise',
            'weight' => 1,  'reps' => 1
        ];
        $response = $this->actingAs($this->user)->postJson('/api/v1/workouts', $workoutDataWithNonExistent);
        $response->assertStatus(201);
        $response->assertJsonPath('data.message', 'Workout saved successfully');
        $response->assertJsonPath('data.ignored_exercises', ['NonExistentExercise']);
        $this->assertDatabaseHas('user_exercise_results',
            [
                'user_id' => $this->user->id,
                'exercise_id' =>  $this->exercises[0]->exercise_id
            ]);

        $this->assertDatabaseHas('user_exercise_results', [
            'user_id' => $this->user->id,
            'exercise_id' =>  $this->exercises[1]->exercise_id
        ]);
    }

    public function test_creates_new_user_exercise_results_if_it_does_not_exist()
    {
        $response = $this->actingAs($this->user)->postJson('/api/v1/workouts', $this->validWorkoutData);
        $response->assertStatus(201);
        $this->assertDatabaseHas('user_exercise_results',
            [
                'user_id' => $this->user->id,  'exercise_id' =>  $this->exercises[0]->exercise_id,
                'record_weight' => 60,  'record_repeats' => 8, 'last_weight' => 60, 'last_repeats' => 8
            ]);

        $this->assertDatabaseHas('user_exercise_results',
            [
                'user_id' =>  $this->user->id,
                'exercise_id' => $this->exercises[1]->exercise_id,  'record_weight' => 70,

                'record_repeats' => 10,  'last_weight' => 70, 'last_repeats' => 10
            ]);
    }


    #[DataProvider('workoutDataProvider')]
    public function test_updates_existing_user_exercise_results(
        int $newWeight, int $newReps, int $expectedRecordWeight, int $expectedRecordReps, int $expectedLastWeight, int $expectedLastReps)
    {
        $existingResult = UserExerciseResult::factory()->create([
            'user_id' => $this->user->id,  'exercise_id' =>  $this->exercises[0]->exercise_id,
            'record_weight' =>  50,  'record_repeats' =>  6,
            'last_weight' =>  50,  'last_repeats' =>  6
        ]);

        $workoutData =  ['exercises' => [[
            'exercise_name' => $this->exercises[0]->exercise_name,
            'weight' => $newWeight, 'reps' =>  $newReps]]
        ];

        $response = $this->actingAs($this->user)->postJson('/api/v1/workouts', $workoutData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('user_exercise_results',  [
            'user_id' => $this->user->id,  'exercise_id' => $this->exercises[0]->exercise_id,
            'record_weight' =>  $expectedRecordWeight,
            'record_repeats' => $expectedRecordReps,
            'last_weight' => $expectedLastWeight,
            'last_repeats' => $expectedLastReps
        ]);
    }

    public static function workoutDataProvider(): array
    {
        return [
            //  Вес больше, повторения больше
            [80, 12, 80, 12, 80, 12],
            //  Вес меньше, повторения меньше
            [20, 5, 50, 6, 20, 5],
            //  Вес равен, повторения больше
            [50, 15, 50, 15, 50, 15],
            // Вес больше, повторения равны
            [90, 6, 90, 6, 90, 6],
            // Вес больше, повторения меньше
            [100, 3, 100, 3, 100, 3],
        ];
    }
}
