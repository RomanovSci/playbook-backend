<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaygroundRentPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playground_rent_prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('playground_id', false, true);
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->integer('price_per_hour');
            $table->char('currency', 3)->default('RUB');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('playground_rent_prices', function (Blueprint $table) {
            $table->foreign('playground_id')
                ->references('id')
                ->on('playgrounds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('playground_rent_prices');
    }
}
