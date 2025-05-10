<?php

namespace Tests\Feature\Workout;

use App\Models\User;
use Database\Seeders\ExercisesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExercisesControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([ExercisesTableSeeder::class]);
        $this->user = User::factory()->create();
    }

    public function test_returns_401_if_user_is_not_authenticated()
    {
        $exerciseName = 'Приседания со штангой на спине';
        $response = $this->getJson("/api/v1/exercises/by-name/{$exerciseName}/guide");
        $response->assertUnauthorized();
    }

    public function testReturnsTutorialUrlForValidExercise(): void
    {
        // Arrange
        $exerciseName = 'Приседания со штангой на спине';
        $tutorialUrl = 'https://youtu.be/O84v0JI24dI?si=8FSrcb_-HrfUewDz';

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/exercises/by-name/{$exerciseName}/guide");

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'tutorial' => $tutorialUrl,
                ],
                'errors' => null,
                'meta' => null,
            ]);
    }

    public function testReturnsErrorWhenExerciseIsNotFound(): void
    {
        // Arrange
        $exerciseName = 'NonExistentExercise';

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/exercises/by-name/{$exerciseName}/guide");

        // Assert
        $response->assertStatus(404)
            ->assertJson([
                'data' => null,
                'errors' => [
                    [
                        'code' => 'EXERCISE_NOT_FOUND',
                        'message' => 'Exercise not found',
                        'meta' => null,
                    ],
                ],
                'meta' => null,
            ]);
    }

    public function testReturnsErrorForAnEmptyExerciseName(): void
    {
        // Arrange
        $exerciseName = '';

        // Act
        $response = $this->actingAs($this->user)->getJson("/api/v1/exercises/by-name/{$exerciseName}/guide");

        // Assert
        $response->assertStatus(404);
    }
}
