<?php
declare(strict_types = 1);

namespace App\Repositories;

use App\Models\Equipment;
use Illuminate\Support\Collection;

/**
 * Class EquipmentRepository
 * @package App\Repositories
 */
class EquipmentRepository extends Repository
{
    protected const MODEL = Equipment::class;

    /**
     * Get by creator uuid
     *
     * @param string $uuid
     * @return mixed
     */
    public function getByCreatorUuid(string $uuid): Collection
    {
        return $this->builder()->where('creator_uuid', $uuid)->get();
    }
}
