<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\TrainerInfo;

/**
 * Class TrainerInfoRepository
 * @package App\Repositories
 */
class TrainerInfoRepository extends Repository
{
    protected const MODEL = TrainerInfo::class;
}
