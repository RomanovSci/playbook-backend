<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\Playground::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'name' => $faker->name,
        'description' => $faker->name,
        'address' => $faker->address,
        'opening_time' => '09:00',
        'closing_time' => '17:00',
        'status' => 0,
        'type_uuid' => null,
        'organization_uuid' => null,
        'creator_uuid' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});