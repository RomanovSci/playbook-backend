<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulesPlaygroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules_playgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('schedule_id', false, true);
            $table->integer('playground_id', false, true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('schedule_id')->references('id')->on('schedules');
            $table->foreign('playground_id')->references('id')->on('playgrounds');
            $table->unique(['schedule_id', 'playground_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules_playgrounds');
    }
}
