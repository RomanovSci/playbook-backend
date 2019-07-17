<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Country;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class MigrateUaCities extends Migration
{
    /**
     * @var integer
     */
    protected $countryUuid;

    /**
     * MigrateUaCities constructor.
     */
    public function __construct()
    {
        $this->countryUuid = Country::where('code', 'UA')->first()->uuid;
    }

    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $filename = resource_path('data/cities/UA.json');
        $cities = json_decode(File::get($filename));

        foreach ($cities as $city) {
            DB::table('cities')->insert([
                'uuid' => Uuid::uuid4(),
                'country_uuid' => $this->countryUuid,
                'name' => $city->name,
                'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
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
            ->where('country_id', $this->countryUuid)
            ->delete();
    }
}
