<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Country;
use App\Models\City;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('country_id', false, true);
            $table->string('name');
            $table->string('origin_name');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->foreign('country_id')
                ->references('id')
                ->on('countries');
        });

        $filename = resource_path('data/cities/ua.json');
        $cities = json_decode(File::get($filename));
        $uaCountryId = Country::where('code', 'UA')->first()->id;

        foreach ($cities as $city) {
            $_city = new City();
            $_city->name = $city->name;
            $_city->origin_name = $city->origin_name;
            $_city->country_id = $uaCountryId;
            $_city->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
