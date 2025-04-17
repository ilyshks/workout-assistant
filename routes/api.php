<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LaggingMuscleGroupsController;
use App\Http\Controllers\UserExerciseProgressController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/v1/register', [AuthController::class, 'register']);
Route::post('/v1/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/v1/users', function (Request $request) {
        return $request->user();
    });

    Route::post('/v1/logout', [AuthController::class, 'logout']);

    Route::get('/v1/user-exercise-progress', [UserExerciseProgressController::class, 'show']);

    Route::post('/v1/workouts', [WorkoutController::class, 'store']);

    Route::get('/v1/lagging-muscle-groups', [LaggingMuscleGroupsController::class, 'index']);
});
