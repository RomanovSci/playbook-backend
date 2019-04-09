<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEquipmentsRentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments_rent', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('booking_uuid');
            $table->uuid('equipment_uuid');
            $table->unsignedInteger('count')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('booking_uuid')->references('uuid')->on('bookings');
            $table->foreign('equipment_uuid')->references('uuid')->on('equipments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipments_rent');
    }
}
