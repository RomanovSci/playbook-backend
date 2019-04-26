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
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_private')->default(false);
            $table->dateTime('start_time')->nullable();
            $table->date('end_time')->nullable();
            $table->dateTime('registration_start_time')->nullable();
            $table->unsignedInteger('max_participants_count')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->char('currency', 3)->default('RUB');
            $table->uuid('tournament_type_uuid');
            $table->uuid('tournament_grid_type_uuid');
            $table->uuid('creator_uuid');
            $table->unsignedBigInteger('challonge_id')
                ->nullable()
                ->comment('External tournament id at https://challonge.com system');
            $table->dateTime('started_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('tournament_type_uuid')->references('uuid')->on('tournaments_types');
            $table->foreign('tournament_grid_type_uuid')->references('uuid')->on('tournaments_grids_types');
            $table->foreign('creator_uuid')->references('uuid')->on('users');
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
