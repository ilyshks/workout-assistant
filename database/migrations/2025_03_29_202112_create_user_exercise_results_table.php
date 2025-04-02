<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_exercise_results', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedSmallInteger('exercise_id');

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('exercise_id')->references('exercise_id')->on('exercises');


            $table->smallInteger('record_weight')->nullable();
            $table->smallInteger('record_repeats')->nullable();
            $table->smallInteger('last_weight')->nullable();
            $table->smallInteger('last_repeats')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('user_exercise_results', function (Blueprint $table) {
            $table->dropForeign(['user_id']);    // Drop foreign key constraint
            $table->dropForeign(['exercise_id']); // Drop foreign key constraint
        });
        Schema::dropIfExists('user_exercise_results');
    }
};
