<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\Organization::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'city_uuid' => null,
        'owner_uuid' => null,
        'name' => $faker->name,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});