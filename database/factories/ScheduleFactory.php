<?php
declare(strict_types = 1);

use Faker\Generator;
use Carbon\Carbon;

$factory->define(App\Models\Schedule::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'start_time' => Carbon::now()->addHours(1)->toDateTimeString(),
        'end_time' => Carbon::now()->addHours(2)->toDateTimeString(),
        'price_per_hour' => $faker->numberBetween(0,2),
        'currency' => 'USD',
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});