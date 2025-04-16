<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Exercise;
use App\Models\UserExerciseResult;

class UserExerciseProgressController extends Controller
{
    public function show(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Пользователь не авторизован'], 401);
        }
        $request->validate([
            'muscle_group' => 'required|string',
            'exercise_name' => 'required|string',
        ]);

        $exercise = Exercise::where('muscle_group', $request->input('muscle_group'))
            ->where('exercise_name', $request->input('exercise_name'))
            ->first();

        if (!$exercise) {
            return response()->json(['message' => 'Упражнение не найдено'], 404);
        }

        $userExerciseResult = UserExerciseResult::where('user_id', $user->id)
            ->where('exercise_id', $exercise->exercise_id)
            ->first();

        if ($userExerciseResult) {
            return response()->json($userExerciseResult); // Возвращаем все поля результата

        } else {
            return response()->
            json(['message' => 'У пользователя нет результатов по этому упражнению', 'data' => null ], 200); }
    }
}
