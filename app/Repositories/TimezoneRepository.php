<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Timezone;

/**
 * Class TimezoneRepository
 * @package App\Repositories
 */
class TimezoneRepository extends Repository
{
    protected const MODEL = Timezone::class;
}
