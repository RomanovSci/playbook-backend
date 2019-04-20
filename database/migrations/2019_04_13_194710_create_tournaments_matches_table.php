<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments_matches', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('tournament_uuid');
            $table->uuid('first_participant_uuid');
            $table->uuid('second_participant_uuid');
            $table->unsignedBigInteger('challonge_id')
                ->nullable()
                ->comment('External match id at https://challonge.com system');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tournament_uuid')->references('uuid')->on('tournaments');
            $table->foreign('first_participant_uuid')->references('uuid')->on('users');
            $table->foreign('second_participant_uuid')->references('uuid')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments_matches');
    }
}
