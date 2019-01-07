<?php

namespace App\Models;

/**
 * Class City
 * @package App\Models
 *
 * @property integer id
 * @property string name
 * @property string origin_name
 * @property integer country_id
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "country_id",
 *                  "name",
 *                  "origin_name",
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="country_id",
 *                  type="integer",
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
