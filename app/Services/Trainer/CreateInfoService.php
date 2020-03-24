<?php
declare(strict_types = 1);

namespace App\Services\Trainer;

use App\Models\TrainerInfo;
use App\Models\User;
use App\Models\UserPlayground;
use App\Services\ExecResult;
use App\Services\File\FileUploadService;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateInfoService
 * @package App\Services\Trainer
 */
class CreateInfoService
{
    /**
     * @var FileUploadService
     */
    protected $fileUploadService;

    /**
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * @param User $user
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public function create(User $user, array $data): ExecResult
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
                $this->fileUploadService->upload('trainer/' . $user->uuid, $data['image'], $info);
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
