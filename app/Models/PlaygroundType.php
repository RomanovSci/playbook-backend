<?php

namespace App\Models;

/**
 * Class PlaygroundType
 *
 * @package App\Models
 * @property string type
 *
 * @OA\Schema(
 *      schema="PlaygroundType",
 *      required={"type"},
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="type",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="deleted_at",
 *          type="string",
 *          readOnly=true
 *      )
 * )
 */
class PlaygroundType extends BaseModel
{
    protected $table = 'playground_types';
}
