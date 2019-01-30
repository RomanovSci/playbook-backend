<?php

namespace App\Models;

/**
 * Class File
 * @package App\Models
 *
 * @property integer id
 * @property integer entity_id
 * @property integer entity_type
 * @property string url
 * @property string name
 * @property string origin_name
 * @property string mime_type
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "entity_id",
 *                  "entity_type",
 *                  "path",
 *                  "name",
 *                  "origin_name",
 *                  "mime_type",
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  description="hidden",
 *              ),
 *              @OA\Property(
 *                  property="entity_id",
 *                  description="hidden",
 *              ),
 *              @OA\Property(
 *                  property="entity_type",
 *                  description="hidden",
 *              ),
 *              @OA\Property(
 *                  property="url",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  description="hidden",
 *              ),
 *              @OA\Property(
 *                  property="origin_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="mime_type",
 *                  description="hidden",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class File extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'files';

    /**
     * @var array
     */
    protected $hidden = [
        'id', 'entity_id', 'entity_type',
        'name', 'mime_type'
    ];
}
