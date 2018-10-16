<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Country;
use App\Models\City;

class MigrateUaCities extends Migration
{
    /**
     * @var integer
     */
    protected $countryId;

    /**
     * MigrateUaCities constructor.
     */
    public function __construct()
    {
        $this->countryId = Country::where('code', 'UA')->first()->id;
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $filename = resource_path('data/cities/ua.json');
        $cities = json_decode(File::get($filename));

        foreach ($cities as $city) {
            $_city = new City();
            $_city->name = $city->name;
            $_city->origin_name = $city->origin_name;
            $_city->country_id = $this->countryId;
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
        City::where('country_id', $this->countryId)
            ->forceDelete();
    }
}
