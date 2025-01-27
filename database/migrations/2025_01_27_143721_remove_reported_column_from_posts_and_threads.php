<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove 'reported' column from 'posts' table
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('reported');
        });

        // Remove 'reported' column from 'threads' table
        Schema::table('threads', function (Blueprint $table) {
            $table->dropColumn('reported');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Add 'reported' column back to 'posts' table
        Schema::table('posts', function (Blueprint $table) {
            $table->boolean('reported')->default(false); // Adjust the default as needed
        });

        // Add 'reported' column back to 'threads' table
        Schema::table('threads', function (Blueprint $table) {
            $table->boolean('reported')->default(false); // Adjust the default as needed
        });
    }
};
