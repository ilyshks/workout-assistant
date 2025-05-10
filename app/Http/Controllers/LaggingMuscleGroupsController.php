<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LaggingMuscleGroupsController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'errors' => [
                    ['code' => 'unauthorized', 'message' => 'Unauthorized']
                ],
                'message' => 'Unauthorized',
                'meta' => null,
                'data' => null,
            ], 401);
        }
        $allMuscleGroups = DB::table('exercises')->pluck('muscle_group')->unique()->toArray();

        $muscleGroupsData = DB::table('user_exercise_results')
            ->join('exercises', 'user_exercise_results.exercise_id', '=', 'exercises.exercise_id')
            ->where('user_exercise_results.user_id', $user->id)
            ->select('exercises.muscle_group',
                DB::raw('MAX(user_exercise_results.updated_at) as last_updated'),
                DB::raw('COUNT(*) as exercises_count')
            )
            ->groupBy('exercises.muscle_group')
            ->get();

        $muscleGroups = [];
        foreach($muscleGroupsData as $groupData){
            $muscleGroups[$groupData->muscle_group] = [
                'last_updated' => $groupData->last_updated,
                'exercises_count' =>  $groupData->exercises_count
            ];
        }

        $weightedMuscleGroups = [];
        foreach($allMuscleGroups as $muscleGroup)
        {
            if (isset($muscleGroups[$muscleGroup])) {
                $weight = $this->calculateWeight($muscleGroups[$muscleGroup]['last_updated'], $muscleGroups[$muscleGroup]['exercises_count']);
            } else {
                $weight = 0;
            }
            $weightedMuscleGroups[$muscleGroup] = $weight;
        }

        asort($weightedMuscleGroups);
        $laggingMuscleGroups = array_keys($weightedMuscleGroups);
        return response()->json([
            'data' => ['lagging_muscle_groups' => $laggingMuscleGroups],
            'meta' => null,
            'errors' => null
        ], 200);
    }
    private function calculateWeight(string  $lastUpdated, int  $exercisesCount): float
    {
        $daysSinceLastUpdate = now()->diffInDays(carbon::parse($lastUpdated));
        return $exercisesCount / (1 +  (-1) * $daysSinceLastUpdate);
    }
}
