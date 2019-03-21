<?php

namespace App\Models;

/**
 * Class File
 * @package App\Models
 *
 * @property integer entity_uuid
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
 *                  "entity_uuid",
 *                  "entity_type",
 *                  "path",
 *                  "name",
 *                  "origin_name",
 *                  "mime_type",
 *              },
 *              @OA\Property(
 *                  property="entity_uuid",
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
     * File constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, [
            'entity_uuid',
            'entity_type',
            'name',
            'mime_type',
        ]);
    }
}
