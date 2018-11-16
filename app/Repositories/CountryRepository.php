<?php

namespace App\Repositories;

use App\Models\Country;

/**
 * Class CountryRepository
 *
 * @package App\Repositories
 */
class CountryRepository
{
    /**
     * Get country by dial code
     *
     * @param $dealCode
     * @return Country[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function getByDialCode($dealCode)
    {
        return Country::where('dial_code', '=', $dealCode)->firstOrFail();
    }
}
