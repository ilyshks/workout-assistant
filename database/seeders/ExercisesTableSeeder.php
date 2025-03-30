<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExercisesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $filePath = database_path('exercises.csv');

        if (!File::exists($filePath)) {
            print("File not found: $filePath\n");
            return;
        }

        $file = fopen($filePath, 'r');

        fgetcsv($file, 0, ';');

        while (($data = fgetcsv($file, 0, ';')) !== false) {
            if (!empty(array_filter($data))) {
                DB::table('exercises')->insert([
                    'muscle_group' => $data[0],
                    'exercise_name' => $data[1],
                    'tutorial' => $data[2]
                ]);
            }
        }

        fclose($file);
    }
}
