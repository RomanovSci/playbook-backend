<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments_requests', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('tournament_uuid');
            $table->uuid('user_uuid');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tournament_uuid')->references('uuid')->on('tournaments');
            $table->foreign('user_uuid')->references('uuid')->on('users');
            $table->unique(['tournament_uuid', 'user_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments_requests');
    }
}
