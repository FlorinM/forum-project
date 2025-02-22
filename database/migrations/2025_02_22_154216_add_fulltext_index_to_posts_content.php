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
        // Adding a FULLTEXT index to the 'content' column
        DB::statement('ALTER TABLE posts ADD FULLTEXT INDEX content_fulltext_index (content)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Dropping the FULLTEXT index
        DB::statement('ALTER TABLE posts DROP INDEX content_fulltext_index');
    }
};
