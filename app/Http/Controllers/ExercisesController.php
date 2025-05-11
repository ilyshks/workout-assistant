<?php

namespace App\Http\Controllers;

use App\Models\Exercise;

class ExercisesController extends Controller
{
    /*
    This method implements the ability to find an exercise by name, returns a link to the guide with youtube.com
    */
    public function show(string $exerciseName)
    {
        $exercise = Exercise::where('exercise_name', $exerciseName)->first();

        if (!$exercise) {
            return response()->json([
                'data' => null,
                'errors' => [
                    [
                        'code' => 'EXERCISE_NOT_FOUND',
                        'message' => 'Exercise not found',
                        'meta' => null,
                    ],
                ],
                'meta' => null,
            ], 404);
        }

        if (!$exercise->tutorial) {
            return response()->json([
                'data' => null,
                'errors' => [
                    [
                        'code' => 'GUIDE_NOT_FOUND',
                        'message' => 'Guide not found for this exercise',
                        'meta' => null,
                    ],
                ],
                'meta' => null,
            ], 404);
        }

        return response()->json([
            'data' => [
                'tutorial' => $exercise->tutorial,
            ],
            'errors' => null,
            'meta' => null,
        ]);
    }
}
