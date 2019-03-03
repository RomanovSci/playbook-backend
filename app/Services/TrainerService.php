<?php

namespace App\Services;

use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use Illuminate\Support\Facades\DB;

/**
 * Class TrainerService
 * @package App\Services
 */
class TrainerService
{
    protected $fileService;

    /**
     * TrainerService constructor.
     *
     * @param FileService $fileService
     */
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Create trainer info
     *
     * @param User $user
     * @param array $data
     * @return TrainerInfo
     * @throws \Throwable
     */
    public function createInfo(User $user, array $data): TrainerInfo
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
                $this->fileService->upload('trainer/' . $user->uuid, $data['image'], $info);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
        return $info;
    }

    /**
     * Edit trainer info
     *
     * @param User $user
     * @param TrainerInfo $info
     * @param array $data
     * @return TrainerInfo
     * @throws \Throwable
     */
    public function editInfo(User $user, TrainerInfo $info, array $data): TrainerInfo
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
                $this->fileService->upload('trainer/' . $user->uuid, $data['image'], $info);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        DB::commit();
        return $info;
    }
}
