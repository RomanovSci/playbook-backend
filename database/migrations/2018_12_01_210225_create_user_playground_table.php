<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPlaygroundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_playground', function (Blueprint $table) {
            $table->uuid('user_uuid');
            $table->uuid('playground_uuid');
            $table->foreign('user_uuid')->references('uuid')->on('users');
            $table->foreign('playground_uuid')->references('uuid')->on('playgrounds');
            $table->unique(['user_uuid', 'playground_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_playground');
    }
}
