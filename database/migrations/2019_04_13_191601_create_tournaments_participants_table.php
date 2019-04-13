<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments_participants', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('nickname');
            $table->uuid('user_uuid');
            $table->uuid('tournament_uuid');
            $table->unsignedBigInteger('challonge_id')
                ->nullable()
                ->comment('External participant id at https://challonge.com system');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_uuid')->references('uuid')->on('users');
            $table->foreign('tournament_uuid')->references('uuid')->on('tournaments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments_participants');
    }
}
