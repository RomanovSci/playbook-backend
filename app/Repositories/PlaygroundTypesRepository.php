<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\PlaygroundType;

/**
 * Class PlaygroundTypesRepository
 * @package App\Repositories
 */
class PlaygroundTypesRepository extends Repository
{
    protected const MODEL = PlaygroundType::class;
}
