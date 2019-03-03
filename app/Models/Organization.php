<?php

namespace App\Models;

/**
 * Class Organization
 * @package App\Models
 *
 * @property string name
 * @property integer owner_uuid
 * @property integer city_uuid
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "owner_uuid",
 *                  "name",
 *                  "city_uuid"
 *              },
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
