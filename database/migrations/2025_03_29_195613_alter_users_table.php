<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop columns
            $table->dropColumn('email');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('password');
            $table->dropColumn('remember_token');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');

            // Rename columns
            $table->renameColumn('id', 'user_id');

            // Modify existing columns
            $table->string('name', 30)->change();

            // Add new columns
            $table->date('date_of_birth')->nullable();
            $table->smallInteger('height')->nullable();
            $table->smallInteger('weight')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Reverse the changes made in up() method.

            $table->dropColumn('date_of_birth');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropTimestamps();

            $table->string('name', 255)->change();

            $table->renameColumn('user_id', 'id');

            // Restore dropped columns (add them back) with original data types and properties where applicable
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();

        });
    }
};
