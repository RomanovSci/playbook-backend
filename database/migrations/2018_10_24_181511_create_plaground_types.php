<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlagroundTypes extends Migration
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
            $model = new \App\Models\PlaygroundType();
            $model->type = $type;
            $model->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Models\PlaygroundType::whereNotNull('id')
            ->forceDelete();
    }
}
