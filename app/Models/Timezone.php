<?php

namespace App\Models;

/**
 * Class Timezone
 * @package App\Models
 *
 * @property integer id
 * @property string value
 * @property string abbreviation
 * @property float offset
 * @property boolean is_dst
 * @property string text
 * @property string utc
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "value",
 *                  "abbreviation",
 *                  "offset",
 *                  "is_dst",
 *                  "text",
 *                  "utc",
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="value",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="abbreviation",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="offset",
 *                  type="float",
 *              ),
 *              @OA\Property(
 *                  property="is_dst",
 *                  type="boolean",
 *              ),
 *              @OA\Property(
 *                  property="text",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="utc",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class Timezone extends BaseModel
{
    protected $table = 'timezones';
}
