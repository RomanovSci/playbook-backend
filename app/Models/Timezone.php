<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class Timezone
 * @package App\Models
 *
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
