<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveSchedulesToEntitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('schedules_to_entities');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('schedules_to_entities', function (Blueprint $table) {
            $table->integer('schedule_id', false, true);
            $table->integer('entity_id', false, true);
            $table->string('entity_type');
            $table->foreign('schedule_id')->references('id')->on('schedules');
            $table->unique(['schedule_id', 'entity_id', 'entity_type']);
        });
    }
}
