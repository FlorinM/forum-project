<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_thread_reads', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('thread_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->nullable();

            // Ensure a user can only have one read record per thread
            $table->unique(['user_id', 'thread_id']);

            $table->timestamps(); // Adds created_at and updated_at timestamps
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_thread_reads');
    }
};

