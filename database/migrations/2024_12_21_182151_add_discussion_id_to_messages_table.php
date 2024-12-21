<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDiscussionIdToMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->foreignId('discussion_id')
            ->after('receiver_id') // Position it after receiver_id
            ->constrained('discussions') // Reference the discussions table
            ->onDelete('cascade'); // Cascade delete messages if the discussion is deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['discussion_id']); // Drop the foreign key constraint
            $table->dropColumn('discussion_id'); // Drop the column
        });
    }
}
