<?php

namespace App\Services;

use App\Models\File;
use App\Objects\Service\ExecResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileService
 * @package App\Services
 */
class FileService
{
    /**
     * Upload file
     *
     * @param string $path
     * @param UploadedFile $uploadedFile
     * @param Model $relatedModel
     * @return ExecResult
     */
    public static function upload(
        string $path,
        UploadedFile  $uploadedFile,
        Model $relatedModel
    ): ExecResult {
        $uploadedFile->store($path);

        $file = new File();
        $file->entity_uuid = $relatedModel->uuid;
        $file->entity_type = get_class($relatedModel);
        $file->name = $uploadedFile->hashName();
        $file->url = Storage::url($path . '/' . $file->name);
        $file->origin_name = $uploadedFile->getClientOriginalName();
        $file->mime_type = $uploadedFile->getMimeType();
        $file->save();

        return ExecResult::instance()->setData([
            'file' => $file,
        ]);
    }
}
