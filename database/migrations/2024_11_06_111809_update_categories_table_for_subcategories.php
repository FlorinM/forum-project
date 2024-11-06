<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCategoriesTableForSubcategories extends Migration
{
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add the parent_id column as a nullable foreign key right after the id column
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('categories')->onDelete('cascade');

            // Modify the user_id foreign key constraint to use RESTRICT instead of CASCADE
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('restrict');
        });
    }

    public function down()
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop the parent_id foreign key constraint and column
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');

            // Restore the original user_id foreign key constraint with ON DELETE CASCADE
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade');
        });
    }
}
