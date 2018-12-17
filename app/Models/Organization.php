<?php

namespace App\Models;

/**
 * Class Organization
 *
 * @package App\Models
 * @property integer id
 * @property string name
 * @property integer owner_id
 * @property integer city_id
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "owner_id",
 *                  "name",
 *                  "city_id"
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="owner_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="city_id",
 *                  type="integer",
 *                  format="int32",
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
        'name', 'owner_id', 'city_id',
    ];
}
