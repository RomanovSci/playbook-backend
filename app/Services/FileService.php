<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

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
     * @return File
     */
    public function upload(
        string $path,
        UploadedFile  $uploadedFile,
        Model $relatedModel
    ): File {
        $uploadedFile->store($path);

        $file = new File();
        $file->entity_id = $relatedModel->id;
        $file->entity_type = get_class($relatedModel);
        $file->path = $path;
        $file->name = $uploadedFile->hashName();
        $file->origin_name = $uploadedFile->getClientOriginalName();
        $file->mime_type = $uploadedFile->getMimeType();
        $file->save();

        return $file;
    }
}
