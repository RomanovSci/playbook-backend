<?php

namespace App\Services\Trainer;

use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use App\Objects\Service\ExecResult;
use App\Services\File\UploadFileService;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateInfoService
 * @package App\Services\Trainer
 */
class CreateInfoService
{
    /**
     * @var UploadFileService
     */
    protected $uploadFileService;

    /**
     * CreateInfoService constructor.
     *
     * @param UploadFileService $uploadFileService
     */
    public function __construct(UploadFileService $uploadFileService)
    {
        $this->uploadFileService = $uploadFileService;
    }

    /**
     * Create trainer info
     *
     * @param User $user
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public function run(User $user, array $data): ExecResult
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
                $this->uploadFileService->run('trainer/' . $user->uuid, $data['image'], $info);
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
