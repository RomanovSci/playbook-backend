<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrganizationIdToPlaygrounds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('playgrounds', function (Blueprint $table) {
            $table->integer('organization_id', false, true);
            $table->foreign('organization_id')
                ->references('id')
                ->on('organizations');
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
            $table->dropColumn('organization_id');
        });
    }
}
