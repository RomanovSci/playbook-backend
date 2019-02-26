<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->uuid('owner_uuid');
            $table->uuid('city_uuid');
            $table->string('name');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('owner_uuid')->references('uuid')->on('users');
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
        Schema::dropIfExists('organizations');
    }
}
