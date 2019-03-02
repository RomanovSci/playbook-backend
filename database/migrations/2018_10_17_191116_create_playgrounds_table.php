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
            $table->uuid('uuid')->primary();
            $table->uuid('organization_uuid')->nullable()->default(null);
            $table->uuid('creator_uuid');
            $table->string('name');
            $table->string('description');
            $table->string('address');
            $table->time('opening_time');
            $table->time('closing_time');
            $table->uuid('type_uuid')->nullable();
            $table->smallInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('organization_uuid')->references('uuid')->on('organizations');
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
        Schema::dropIfExists('playgrounds');
    }
}
