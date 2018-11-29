<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaygroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playgrounds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('organization_id', false, true)->nullable()->default(null);
            $table->integer('creator_id', false, true);
            $table->string('name');
            $table->string('description');
            $table->string('address');
            $table->time('opening_time');
            $table->time('closing_time');
            $table->integer('type_id')->unsigned();
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('organization_id')->references('id')->on('organizations');
            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playgrounds');
    }
}
