<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class City
 * @package App\Models
 *
 * @property string name
 * @property string origin_name
 * @property string country_uuid
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="country_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class City extends BaseModel
{
    protected $table = 'cities';
}
