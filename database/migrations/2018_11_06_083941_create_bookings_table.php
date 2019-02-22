<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bookable_id', false, true);
            $table->string('bookable_type');
            $table->integer('creator_id', false, true);
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('note')->nullable();
            $table->integer('price');
            $table->char('currency', 3)->default('RUB');
            $table->smallInteger('status')->default(0);
            $table->integer('playground_id', false, true)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('users');
            $table->foreign('playground_id')->references('id')->on('playgrounds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}
