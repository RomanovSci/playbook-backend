<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsInvitationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments_invitations', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('tournament_uuid');
            $table->uuid('inviter_uuid');
            $table->uuid('invited_uuid');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tournament_uuid')->references('uuid')->on('tournaments');
            $table->foreign('inviter_uuid')->references('uuid')->on('users');
            $table->foreign('invited_uuid')->references('uuid')->on('users');
            $table->unique(['tournament_uuid', 'inviter_uuid', 'invited_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments_invitations');
    }
}
