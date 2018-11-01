<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class CreatePlaygroundTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            'ground',
            'grass',
            'hard',
            'artificial-grass',
            'wood',
            'asphalt',
            'rubber',
        ];

        foreach ($types as $type) {
            DB::table('playground_types')->insert([
                'type' => $type,
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
        DB::table('playground_types')->truncate();
    }
}
