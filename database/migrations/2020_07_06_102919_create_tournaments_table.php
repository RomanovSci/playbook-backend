<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('sport');
            $table->string('category');
            $table->unsignedInteger('price')->default(0);
            $table->char('currency', 3)->default('RUB');
            $table->uuid('creator_uuid');
            $table->uuid('tournament_type_uuid');
            $table->boolean('third_place_match')->default(false);
            $table->unsignedInteger('players_count_in_group')->nullable();
            $table->unsignedInteger('players_count_in_playoff')->nullable();
            $table->json('metadata')->nullable();
            $table->json('state')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('started_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('creator_uuid')->references('uuid')->on('users');
            $table->foreign('tournament_type_uuid')->references('uuid')->on('tournament_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tournaments');
    }
}
