<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixOpeningAndClosingTimeColumnInPlaygroundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playgrounds', function (Blueprint $table) {
            $table->time('opening_time')->change();
            $table->time('closing_time')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('playgrounds', function (Blueprint $table) {
            $table->timestamp('opening_time')->change();
            $table->timestamp('closing_time')->change();
        });
    }
}
