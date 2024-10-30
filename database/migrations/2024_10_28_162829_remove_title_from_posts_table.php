<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTitleFromPostsTable extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('title'); // Remove the title column
        });
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('title'); // Add the title column back if rolling back
        });
    }
}

