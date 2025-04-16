<?php

namespace Workout;

use App\Models\Exercise;
use App\Models\User;
use App\Models\UserExerciseResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Seeders\ExercisesTableSeeder;


class UserExerciseProgressControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Exercise $exercise;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ExercisesTableSeeder::class]);
        $this->user = User::factory()->create();
        $this->exercise = Exercise::first();

    }

    public function test_returns_401_unauthorized_error_if_user_is_not_logged_in()
    {
        $response = $this->getJson('/api/user-exercise-progress');
        $response->assertUnauthorized();
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_returns_404_if_exercise_does_not_exist()
    {
        $response = $this->actingAs($this->user)
            ->getJson('/api/user-exercise-progress',
                [
                    'muscle_group' => 'Руки',
                    'exercise_name' => 'Подъем гантелей на бицепс']);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'muscle_group',
                'exercise_name',

            ]
        ]);
    }

    public function test_returns_200_ok_and_null_if_user_has_no_results()
    {
        $exercise = $this->exercise;

        $response = $this->actingAs($this->user)
            ->getJson('/api/user-exercise-progress?muscle_group=' . urlencode($exercise->muscle_group) .
                '&exercise_name=' .  urlencode($exercise->exercise_name) );

        $response->assertOk();
        $response->assertJson([
            'message' => 'У пользователя нет результатов по этому упражнению',
            'data' => null
        ]);
    }

    public function test_returns_all_fields_if_user_and_exercise_exists()
    {

        $userExerciseResult = UserExerciseResult::factory()->create([
            'user_id' => $this->user->id,
            'exercise_id' => $this->exercise->exercise_id
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/user-exercise-progress?muscle_group=' . urlencode($this->exercise->muscle_group) .
                '&exercise_name=' .  urlencode($this->exercise->exercise_name) );

        $response->assertOk();

        $response->assertJson($userExerciseResult->toArray());

    }

    public function test_returns_validation_errors_if_required_data_not_provided()

    {

        $response = $this->actingAs($this->user)->getJson('/api/user-exercise-progress');
        $response->assertStatus(422);

    }
}
