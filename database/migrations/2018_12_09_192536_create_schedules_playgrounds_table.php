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
            $table->uuid('uuid')->unique();
            $table->uuid('schedule_uuid');
            $table->uuid('playground_uuid');
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('schedules_playgrounds');
    }
}
