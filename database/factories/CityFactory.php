<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\City::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'country_uuid' => null,
        'name' => $faker->name,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});