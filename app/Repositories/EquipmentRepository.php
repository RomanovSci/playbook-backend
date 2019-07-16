<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Equipment;
use Illuminate\Support\Collection;

/**
 * Class EquipmentRepository
 * @package App\Repositories
 */
class EquipmentRepository
{
    /**
     * Get by creator uuid
     *
     * @param string $uuid
     * @return mixed
     */
    public static function getByCreatorUuid(string $uuid): Collection
    {
        return Equipment::where('creator_uuid', $uuid)->get();
    }
}
