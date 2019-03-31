<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('creator_uuid');
            $table->string('name');
            $table->unsignedInteger('price_per_hour')->default(0);
            $table->char('currency', 3)->default('RUB');
            $table->unsignedInteger('count')->default(0);
            $table->timestamps();
            $table->softDeletes();
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
        Schema::dropIfExists('equipments');
    }
}
