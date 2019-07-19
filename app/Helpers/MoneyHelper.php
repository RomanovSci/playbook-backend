<?php
declare(strict_types = 1);

namespace App\Helpers;

use App\Models\Equipment;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Model;

/**
 * Class MoneyHelper
 * @package App\Helpers
 */
class MoneyHelper
{
    /**
     * Return entity price per one minute
     *
     * @param Model|Schedule|Equipment $entity
     * @return int
     */
    public static function getMinutesRate(Model $entity): int
    {
        return (int) money($entity->price_per_hour, $entity->currency)
            ->divide(60)
            ->getAmount();
    }
}
