<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\PlaygroundType::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'type' => $faker->uuid,
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});