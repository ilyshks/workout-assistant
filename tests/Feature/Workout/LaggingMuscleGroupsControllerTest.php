<?php

namespace Tests\Feature;

use App\Models\Exercise;
use App\Models\User;
use App\Models\UserExerciseResult;
use Carbon\Carbon;
use Database\Seeders\ExercisesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LaggingMuscleGroupsControllerTest extends TestCase
{
    use RefreshDatabase;
    private User  $user;
    protected function setUp():  void
    {
        parent::setUp();
        $this->seed([ExercisesTableSeeder::class]);
        $this->user = User::factory()->create();
    }

    public function test_returns_401_if_user_is_not_authenticated()
    {
        $response =  $this->getJson('/api/lagging-muscle-groups');
        $response->assertUnauthorized();
    }

    #[DataProvider('laggingMuscleGroupsDataProvider')]
    public function test_returns_lagging_muscle_groups(array $exercises, array $dates, array $expectedLaggingMuscleGroups)
    {
        foreach ($exercises as $key => $exerciseData) {
            $exercise = Exercise::where('muscle_group', $exerciseData['muscle_group'])
                ->where('exercise_name', $exerciseData['exercise_name'])
                ->first();

            $this->assertNotNull($exercise, "Exercise not found: " . $exerciseData['exercise_name']);

            UserExerciseResult::factory()->create([
                'user_id' => $this->user->id,
                'exercise_id' => $exercise->exercise_id,
                'record_weight' => 10,
                'record_repeats' => 5,
                'last_weight' => 8,
                'last_repeats' => 6,
                'created_at' => $dates[$key]['created_at'],
                'updated_at' => $dates[$key]['updated_at']
            ]);
        }

        $response = $this->actingAs($this->user)->getJson('/api/lagging-muscle-groups');

        $response->assertOk();
        $response->assertJson(['lagging_muscle_groups' => $expectedLaggingMuscleGroups]);
    }

    public static function laggingMuscleGroupsDataProvider(): array
    {
        return [
            'no_exercises' =>  [[], [], ["Ноги и Ягодицы", "Спина", "Грудь", "Плечи", "Руки", "Пресс"]],
            'one_muscle_group' => [
                [['muscle_group' => 'Ноги и Ягодицы', 'exercise_name' =>  'Приседания со штангой на спине']],
                [['created_at' => now(), 'updated_at' =>  now()]],
                ["Спина", "Грудь", "Плечи", "Руки", "Пресс", "Ноги и Ягодицы"]
            ],
            'multiple_muscle_groups_sorted_by_date' =>
                [
                    [['muscle_group' => 'Ноги и Ягодицы', 'exercise_name' => 'Приседания с гантелей'],
                        ['muscle_group' =>  'Спина',  'exercise_name' => 'Подтягивания (на спину)']],
                    [['created_at' => now()->subDays(5), 'updated_at' =>  now()->subDays(5)],
                        ['created_at' => now(), 'updated_at' =>  now()]],
                    ["Грудь", "Плечи", "Руки", "Пресс", 'Ноги и Ягодицы', 'Спина']
                ],
            'multiple_muscle_groups_sorted_by_exercises_count' => [
                [['muscle_group' =>  'Ноги и Ягодицы',  'exercise_name' =>  'Становая тяга (классика)'],
                    ['muscle_group' =>  'Ноги и Ягодицы', 'exercise_name' => 'Румынская тяга'],
                    ['muscle_group' =>  'Грудь', 'exercise_name' => 'Жим гантелей лёжа']],
                [['created_at' => now(), 'updated_at' =>  now()],
                    ['created_at' => now(), 'updated_at' =>  now()],
                    ['created_at' => now(), 'updated_at' =>  now()]],
                ["Спина", "Плечи", "Руки", "Пресс", 'Грудь', 'Ноги и Ягодицы']
            ],

        ];

    }
}
