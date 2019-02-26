<?php

namespace App\Models;

/**
 * Class PlaygroundType
 * @package App\Models
 *
 * @property string type
 *
 * @OA\Schema(
 *      required={"type"},
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="type",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class PlaygroundType extends BaseModel
{
    protected $table = 'playground_types';
}
