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
            $table->uuid('uuid')->unique();
            $table->uuid('bookable_uuid');
            $table->string('bookable_type');
            $table->uuid('creator_uuid');
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('note')->nullable();
            $table->integer('price');
            $table->char('currency', 3)->default('RUB');
            $table->smallInteger('status')->default(0);
            $table->uuid('playground_uuid')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_uuid')->references('uuid')->on('users');
            $table->foreign('playground_uuid')->references('uuid')->on('playgrounds');
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
