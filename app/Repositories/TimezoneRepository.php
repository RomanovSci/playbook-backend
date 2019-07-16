<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Timezone;

/**
 * Class TimezoneRepository
 * @package App\Repositories
 */
class TimezoneRepository
{
    /**
     * @param string $name
     * @return Timezone
     */
    public static function getFirstByName(string $name): ?Timezone
    {
        return Timezone::where('utc', $name)->first();
    }
}
