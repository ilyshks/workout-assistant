<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExercisesController;
use App\Http\Controllers\LaggingMuscleGroupsController;
use App\Http\Controllers\UserExerciseProgressController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::get('/users', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user-exercise-progress', [UserExerciseProgressController::class, 'show']);

    Route::post('/workouts', [WorkoutController::class, 'store']);

    Route::get('/lagging-muscle-groups', [LaggingMuscleGroupsController::class, 'index']);

    Route::get('/exercises/by-name/{exercise_name}/guide', [ExercisesController::class, 'show']);
});
