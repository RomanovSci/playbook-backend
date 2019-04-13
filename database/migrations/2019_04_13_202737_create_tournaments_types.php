<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class CreateTournamentsTypes extends Migration
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
            DB::table('tournaments_types')->insert([
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
        DB::table('tournaments_types')->delete();
    }
}
