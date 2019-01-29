<?php

namespace App\Models;

/**
 * Class File
 * @package App\Models
 *
 * @property integer id
 * @property integer entity_id
 * @property integer entity_type
 * @property string path
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
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="entity_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="entity_type",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="path",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="origin_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="mime_type",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class File extends BaseModel
{
    protected $table = 'files';
}
