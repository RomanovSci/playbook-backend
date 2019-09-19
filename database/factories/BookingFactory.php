<?php
declare(strict_types = 1);

use Faker\Generator;
use Carbon\Carbon;

$factory->define(App\Models\Booking::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'bookable_uuid' => null,
        'bookable_type' => null,
        'creator_uuid' => null,
        'start_time' => Carbon::now()->addDays(1)->toDateTimeString(),
        'end_time' => Carbon::now()->addDays(2)->toDateTimeString(),
        'price' => 1,
        'currency' => $faker->currencyCode,
        'players_count' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});