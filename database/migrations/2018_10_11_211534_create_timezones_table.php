<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

class CreateTimezonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        Schema::create('timezones', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
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
                    'uuid' => Uuid::uuid4(),
                    'value' => $timezone->value,
                    'abbreviation' => $timezone->abbr,
                    'offset' => $timezone->offset,
                    'is_dst' => $timezone->isdst,
                    'text' => $timezone->text,
                    'utc' => $utc,
                    'created_at' => DB::raw('CURRENT_TIMESTAMP'),
                    'updated_at' => DB::raw('CURRENT_TIMESTAMP'),
                ]);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('timezone_uuid')->references('uuid')->on('timezones');
        });
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
