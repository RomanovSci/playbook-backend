<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\Country::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'code' => $faker->currencyCode,
        'name' => $faker->name,
        'dial_code' => '+380',
        'created_at' => now(),
        'updated_at' => now(),
    ];
});