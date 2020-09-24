<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class CreateTournamentTypesTable extends Migration
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
            'singleElimination',
            'groupStage',
            'groupTbDuel',
        ];

        Schema::create('tournament_types', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('title');
            $table->timestamps();
            $table->softDeletes();
        });

        foreach ($types as $type) {
            DB::table('tournament_types')->insert([
                'uuid' => Uuid::uuid4(),
                'title' => $type,
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
        Schema::dropIfExists('tournament_types');
    }
}
