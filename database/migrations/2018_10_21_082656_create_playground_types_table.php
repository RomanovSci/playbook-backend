<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlaygroundTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('playground_types', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('type')->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('playgrounds', function (Blueprint $table) {
            $table->foreign('type_uuid')->references('uuid')->on('playground_types');
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
            $table->dropForeign('type_id');
        });

        Schema::dropIfExists('playground_types');
    }
}
