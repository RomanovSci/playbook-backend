<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulePlaygroundTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule_playground', function (Blueprint $table) {
            $table->uuid('schedule_uuid');
            $table->uuid('playground_uuid');
            $table->foreign('schedule_uuid')->references('uuid')->on('schedules');
            $table->foreign('playground_uuid')->references('uuid')->on('playgrounds');
            $table->unique(['schedule_uuid', 'playground_uuid']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedule_playground');
    }
}
