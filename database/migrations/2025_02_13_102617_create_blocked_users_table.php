<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blocked_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // The user who blocks
            $table->foreignId('blocked_user_id')->constrained('users')->onDelete('cascade'); // The blocked user
            $table->timestamps();

            // Ensure a user can't block the same user twice
            $table->unique(['user_id', 'blocked_user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocked_users');
    }
};
