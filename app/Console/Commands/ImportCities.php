<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ImportCities
 * @package App\Console\Commands
 */
class ImportCities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cities:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate cities table and import new from json files';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        DB::table('cities')->delete();
        $files = array_slice(scandir(resource_path('data/cities')), 2);

        foreach ($files as $file) {
            $country = Country::where('code', str_replace('.json', '', $file))->first();
            $cities = json_decode(\File::get(resource_path('data/cities/') . $file));

            foreach ($cities as $city) {
                DB::table('cities')->insert([
                    'country_id' => $country->id,
                    'name' => $city->name,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                ]);
            }
        }
    }
}
