<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersPlaygroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_playgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id', false, true);
            $table->integer('playground_id', false, true);
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('playground_id')->references('id')->on('playgrounds');
            $table->unique(['user_id', 'playground_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_playgrounds');
    }
}
