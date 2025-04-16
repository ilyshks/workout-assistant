<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Exercise;
use App\Models\UserExerciseResult;
use Throwable;

class WorkoutController extends Controller
{
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $ignoredExercises = [];

        try {
            DB::beginTransaction();

            $user = Auth::user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $request->validate([
                'exercises' => 'required|array',
                'exercises.*.exercise_name' => 'required|string',
                'exercises.*.weight' => 'required|numeric',
                'exercises.*.reps' => 'required|integer'
            ]);

            foreach ($request->input('exercises', []) as $exerciseData) {
                $exercise = Exercise::where('exercise_name', $exerciseData['exercise_name'])->first();
                if (!$exercise) {
                    $ignoredExercises[] = $exerciseData['exercise_name'];
                    continue;
                }

                $userExerciseResult = UserExerciseResult::where('user_id', $user->id)
                    ->where('exercise_id', $exercise->exercise_id)
                    ->lockForUpdate() // Блокировка записи
                    ->first();

                if (!$userExerciseResult) {
                    // Создаем новую запись
                    UserExerciseResult::create([
                        'user_id' => $user->id,
                        'exercise_id' => $exercise->exercise_id,
                        'record_weight' => $exerciseData['weight'],
                        'record_repeats' => $exerciseData['reps'],
                        'last_weight' => $exerciseData['weight'],
                        'last_repeats' => $exerciseData['reps']
                    ]);
                } else {
                    // Обновляем существующую запись
                    $userExerciseResult->record_weight = max($userExerciseResult->record_weight, $exerciseData['weight']);

                    if($userExerciseResult->record_weight > $userExerciseResult->last_weight){
                        $userExerciseResult->record_repeats = $exerciseData['reps'];
                    } else if ($userExerciseResult->record_weight == $userExerciseResult->last_weight){
                        $userExerciseResult->record_repeats = max($userExerciseResult->record_repeats, $exerciseData['reps']);
                    }
                    $userExerciseResult->last_weight = $exerciseData['weight'];
                    $userExerciseResult->last_repeats = $exerciseData['reps'];
                    $userExerciseResult->save();
                }
            }
            DB::commit();
            return response()->json([
                'message' => 'Workout saved successfully',
                'ignored_exercises' => $ignoredExercises], 201);
        } catch (QueryException  $e) {
            DB::rollBack();
            return response()->json(['error' => 'Database error: ' . $e->getMessage()], 500);
        }
    }
}
