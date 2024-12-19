<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname')->nullable()->after('name');
            $table->date('birthday')->nullable()->after('password');
            $table->enum('gender', ['male', 'female', 'other'])->nullable()->after('birthday');
            $table->text('description')->nullable()->after('gender');
            $table->string('signature')->nullable()->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nickname', 'birthday', 'gender', 'description', 'signature']);
        });
    }
};
