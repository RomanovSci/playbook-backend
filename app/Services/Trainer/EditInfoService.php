<?php

namespace App\Services\Trainer;

use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use App\Objects\Service\ExecResult;
use App\Services\File\UploadFileService;
use Illuminate\Support\Facades\DB;

/**
 * Class EditInfoService
 * @package App\Services\Trainer
 */
class EditInfoService
{
    /**
     * @var UploadFileService
     */
    protected $uploadFileService;

    /**
     * EditInfoService constructor.
     *
     * @param UploadFileService $uploadFileService
     */
    public function __construct(UploadFileService $uploadFileService)
    {
        $this->uploadFileService = $uploadFileService;
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
    public function run(User $user, TrainerInfo $info, array $data): ExecResult
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
