<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('country_uuid');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('country_uuid')->references('uuid')->on('countries');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('city_uuid')->references('uuid')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
