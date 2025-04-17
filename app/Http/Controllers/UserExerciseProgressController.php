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
            return response()->json([
                'errors' => [['code' => 'unauthorized', 'message' => 'Пользователь не авторизован']],
                'data' => null,
                'meta' => null,
            ], 401);
        }
        try {
            $validated = $request->validate([
                'muscle_group' => 'required|string',
                'exercise_name' => 'required|string',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'errors' =>  [array('code' => 'validation_error', 'message' => $e->getMessage(),  'meta' =>  $e->errors())],
                'data' => null,
                'meta' => null,

            ], 422);

        }

        $exercise = Exercise::where('muscle_group', $request->input('muscle_group'))
            ->where('exercise_name', $request->input('exercise_name'))
            ->first();

        if (!$exercise) {
            return response()->json([
                'errors' => [['code' => 'exercise_not_found', 'message' => 'Упражнение не найдено']],
                'data' => null,
                'meta' => null,
            ], 404);
        }

        $userExerciseResult = UserExerciseResult::where('user_id', $user->id)
            ->where('exercise_id', $exercise->exercise_id)
            ->first();

        if ($userExerciseResult) {
            return response()->json([
                'data' => $userExerciseResult,
                'meta' => null,
            ]);
        } else {
            return response()->json([
                'data' => null,
                'meta' => null,
            ], 200);
        }
    }
}
