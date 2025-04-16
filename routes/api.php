<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserExerciseProgressController;
use App\Http\Controllers\WorkoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user-exercise-progress', [UserExerciseProgressController::class, 'show']);

    Route::post('/workout', [WorkoutController::class, 'store']);
});
