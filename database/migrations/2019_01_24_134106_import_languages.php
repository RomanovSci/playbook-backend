<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class ImportLanguages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $languages = json_decode(File::get(resource_path('data/languages.json')), true);

        foreach ($languages as $languageCode => $languageData) {
            DB::table('languages')->insert([
                'code' => $languageCode,
                'name' => $languageData['name'],
                'native_name' => $languageData['native_name'],
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
        DB::table('languages')->delete();
    }
}
