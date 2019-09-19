<?php
declare(strict_types = 1);

use Faker\Generator;

$factory->define(App\Models\TrainerInfo::class, function (Generator $faker) {
    return [
        'uuid' => $faker->uuid,
        'user_uuid' => null,
        'about' => $faker->text,
        'min_price' => 1,
        'max_price' => 2,
        'currency' => $faker->currencyCode,
        'created_at' => now(),
        'updated_at' => now(),
    ];
});