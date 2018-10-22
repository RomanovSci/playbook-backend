<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPlagroundRentPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playground_rent_prices', function (Blueprint $table) {
            $table->renameColumn('start_date', 'start_time');
            $table->renameColumn('end_date', 'end_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playground_rent_prices', function (Blueprint $table) {
            $table->renameColumn('start_time', 'start_date');
            $table->renameColumn('end_time', 'end_date');
        });
    }
}
