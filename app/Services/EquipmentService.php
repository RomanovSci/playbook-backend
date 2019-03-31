<?php

namespace App\Services;

use App\Models\Equipment;
use App\Models\User;
use App\Objects\Service\ExecResult;

/**
 * Class EquipmentService
 * @package App\Services
 */
class EquipmentService
{
    public static function create(User $user, array $data): ExecResult
    {
        /** @var Equipment $equipment */
        $equipment = Equipment::create(array_merge($data, [
            'creator_uuid' => $user->uuid,
        ]));

        return ExecResult::instance()
            ->setSuccess()
            ->setData($equipment->toArray());
    }
}
