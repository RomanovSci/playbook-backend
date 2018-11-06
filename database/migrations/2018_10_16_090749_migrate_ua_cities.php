<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Country;
use Illuminate\Support\Facades\DB;

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
            DB::table('cities')->insert([
                'country_id' => $this->countryId,
                'name' => $city->name,
                'origin_name' => $city->origin_name,
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
        DB::table('cities')
            ->where('country_id', $this->countryId)
            ->delete();
    }
}
