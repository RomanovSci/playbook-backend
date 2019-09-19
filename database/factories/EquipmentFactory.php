<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\Equipment::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'creator_uuid' => null,
        'name' => $faker->domainName,
        'price_per_hour' => 1000,
        'currency' => $faker->currencyCode,
        'availability' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});