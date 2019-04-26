<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Ramsey\Uuid\Uuid;

class CreateTournamentsGridsTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        $types = [
            'single elimination',
            'double elimination',
            'round robin',
            'swiss'
        ];

        foreach ($types as $type) {
            DB::table('tournaments_grids_types')->insert([
                'uuid' => Uuid::uuid4(),
                'name' => $type,
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
        DB::table('tournament_grid_types')->delete();
    }
}
