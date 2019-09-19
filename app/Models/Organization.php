<?php
declare(strict_types = 1);

namespace App\Models;

use Ramsey\Uuid\Uuid;

/**
 * Class Organization
 * @package App\Models
 *
 * @property string name
 * @property Uuid owner_uuid
 * @property Uuid city_uuid
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="owner_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="city_uuid",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Organization extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'organizations';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'owner_uuid',
        'city_uuid',
    ];
}
