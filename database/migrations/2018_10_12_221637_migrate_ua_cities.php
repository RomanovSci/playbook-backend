<?php

use Illuminate\Support\Facades\File;
use Illuminate\Database\Migrations\Migration;
use App\Models\City;
use App\Models\Country;

class MigrateUaCities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $filename = resource_path('data/cities/ua.json');
        $regions = json_decode(File::get($filename));
        $uaCountryId = Country::where('code', 'UA')->first()->id;

        foreach ($regions as $region) {
            foreach ($region->areas as $area) {
                $city = new City();
                $city->name = $area->name;
                $city->country_id = $uaCountryId;
                $city->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
