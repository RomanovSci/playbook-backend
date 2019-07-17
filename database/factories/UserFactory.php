<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\User::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'middle_name' => $faker->lastName,
        'phone' => $faker->randomNumber(9),
        'created_at' => $faker->dateTime,
        'updated_at' => $faker->dateTime,
    ];
});
