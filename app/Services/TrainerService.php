<?php

namespace App\Services;

use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use App\Objects\Service\ExecResult;
use Illuminate\Support\Facades\DB;

/**
 * Class TrainerService
 * @package App\Services
 */
class TrainerService
{
    /**
     * Create trainer info
     *
     * @param User $user
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public static function createInfo(User $user, array $data): ExecResult
    {
        try {
            DB::beginTransaction();
            $info = TrainerInfo::create(array_merge($data, [
                'user_uuid' => $user->uuid,
            ]));

            foreach ($data['playgrounds'] as $playgroundUuid) {
                UserPlayground::create([
                    'user_uuid' => $user->uuid,
                    'playground_uuid' => $playgroundUuid
                ]);
            }

            if (isset($data['image'])) {
                FileService::upload('trainer/' . $user->uuid, $data['image'], $info);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
        return ExecResult::instance()
            ->setSuccess()
            ->setData([
                'info' => $info
            ]);
    }

    /**
     * Edit trainer info
     *
     * @param User $user
     * @param TrainerInfo $info
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public static function editInfo(User $user, TrainerInfo $info, array $data): ExecResult
    {
        try {
            DB::beginTransaction();
            $info->fill($data)->update();
            UserPlayground::where('user_uuid', $user->uuid)->delete();

            foreach ($data['playgrounds'] as $playgroundUuid) {
                UserPlayground::create([
                    'user_uuid' => $user->uuid,
                    'playground_uuid' => $playgroundUuid
                ]);
            }

            if (isset($data['image'])) {
                $info->images()->delete();
                FileService::upload('trainer/' . $user->uuid, $data['image'], $info);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
        return ExecResult::instance()
            ->setSuccess()
            ->setData([
                'info' => $info
            ]);
    }
}
