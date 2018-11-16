<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateCountriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->increments('id');
            $table->char('code', 2);
            $table->string('name');
            $table->string('dial_code');
            $table->timestamps();
            $table->softDeletes();
        });

        $filename = resource_path('data/countries.json');
        $countries = json_decode(File::get($filename));

        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'code' => $country->code,
                'name' => $country->name,
                'dial_code' => $country->dial_code,
                'created_at' => DB::raw('NOW()'),
                'updated_at' => DB::raw('NOW()'),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
