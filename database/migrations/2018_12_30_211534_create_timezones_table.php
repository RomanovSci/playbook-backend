<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
            $table->string('abbreviation');
            $table->float('offset');
            $table->boolean('is_dst');
            $table->string('text');
            $table->string('utc');
            $table->timestamps();
            $table->softDeletes();
        });

        $filename = resource_path('data/timezones.json');
        $timezones = json_decode(File::get($filename));

        foreach ($timezones as $timezone) {
            foreach ($timezone->utc as $utc) {
                DB::table('timezones')->insert([
                    'value' => $timezone->value,
                    'abbreviation' => $timezone->abbr,
                    'offset' => $timezone->offset,
                    'is_dst' => $timezone->isdst,
                    'text' => $timezone->text,
                    'utc' => $utc,
                    'created_at' => DB::raw('NOW()'),
                    'updated_at' => DB::raw('NOW()'),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timezones');
    }
}
