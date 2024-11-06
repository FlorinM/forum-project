<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDescriptionToCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Adding description column
            $table->text('description')->nullable()->after('name');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Dropping description column in case of rollback
            $table->dropColumn('description');
        });
    }
}
