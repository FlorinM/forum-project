<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Drop the existing foreign key constraint on 'post_id' in the reports table
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });

        // Add the foreign key constraint again with 'ON DELETE RESTRICT'
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Drop the modified foreign key constraint in case of rollback
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['post_id']);
        });

        // Revert to the original foreign key with 'ON DELETE CASCADE'
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('post_id')
                  ->references('id')->on('posts')
                  ->onDelete('cascade');
        });
    }
};
