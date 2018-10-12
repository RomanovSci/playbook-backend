<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Country;

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
            $table->timestamps();
            $table->softDeletes();
        });

        $filename = resource_path('data/countries.json');
        $countries = json_decode(File::get($filename));

        foreach ($countries as $country) {
            $countryModel = new Country();
            $countryModel->code = $country->alpha2Code;
            $countryModel->name = $country->name;
            $countryModel->save();
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
