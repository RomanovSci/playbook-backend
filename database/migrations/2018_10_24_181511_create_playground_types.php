<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;
use Ramsey\Uuid\Uuid;

class CreatePlaygroundTypes extends Migration
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
                'uuid' => Uuid::uuid4(),
                'type' => $type,
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
        DB::table('playground_types')->truncate();
    }
}
