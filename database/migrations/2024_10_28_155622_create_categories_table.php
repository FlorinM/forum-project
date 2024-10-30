<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id(); // BIGINT UNSIGNED AUTO_INCREMENT
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // User who created the category
            $table->string('name');
            $table->timestamps(0); // created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
