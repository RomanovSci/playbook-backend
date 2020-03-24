<?php
declare(strict_type = 1);

namespace App\Services\File;

use App\Models\File;
use App\Services\ExecResult;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Class FileService
 * @package App\Services\File
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
    public function upload(string $path, UploadedFile  $uploadedFile, Model $relatedModel): ExecResult
    {
        $uploadedFile->store($path);

        $file = new File();
        $file->entity_uuid = $relatedModel->uuid;
        $file->entity_type = get_class($relatedModel);
        $file->name = $uploadedFile->hashName();
        $file->url = Storage::url($path . '/' . $file->name);
        $file->origin_name = $uploadedFile->getClientOriginalName();
        $file->mime_type = $uploadedFile->getMimeType();
        $file->save();

        return ExecResult::instance()->setData(['file' => $file]);
    }
}
