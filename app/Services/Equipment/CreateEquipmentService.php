<?php
declare(strict_type = 1);

namespace App\Services\Equipment;

use App\Models\Equipment;
use App\Models\User;
use App\Services\ExecResult;

/**
 * Class EquipmentService
 * @package App\Services\Equipment
 */
class EquipmentService
{
    /**
     * @param User $user
     * @param array $data
     * @return ExecResult
     */
    public function create(User $user, array $data): ExecResult
    {
        /** @var Equipment $equipment */
        $equipment = Equipment::create(array_merge($data, ['creator_uuid' => $user->uuid]));

        return ExecResult::instance()
            ->setSuccess()
            ->setData($equipment->toArray());
    }
}
