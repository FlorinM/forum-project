
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscussionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discussions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade'); // Foreign key for initiator
            $table->foreignId('participant_id')->constrained('users')->onDelete('cascade'); // Foreign key for participant
            $table->string('subject', 255)->nullable(); // Subject column
            $table->timestamp('initiator_deleted_at')->nullable(); // Soft delete for initiator
            $table->timestamp('participant_deleted_at')->nullable(); // Soft delete for participant
            $table->timestamps(); // Created and updated timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discussions');
    }
}
