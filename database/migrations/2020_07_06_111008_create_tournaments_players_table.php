<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsPlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments_players', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tournament_uuid');
            $table->uuid('user_uuid')->nullable();
            $table->string('first_name');
            $table->string('last_name');
            $table->unsignedSmallInteger('order');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments_players');
    }
}
