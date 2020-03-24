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
 * Class EditInfoService
 * @package App\Services\Trainer
 */
class EditInfoService
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
     * @param TrainerInfo $info
     * @param array $data
     * @return ExecResult
     * @throws \Throwable
     */
    public function edit(User $user, TrainerInfo $info, array $data): ExecResult
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
