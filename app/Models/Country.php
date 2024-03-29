<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class Country
 * @package App\Models
 *
 * @property string code
 * @property string name
 * @property string origin_name
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="dial_code",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Country extends BaseModel
{
    protected $table = 'countries';
}
