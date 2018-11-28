<?php

namespace App\Models;

/**
 * Class City
 *
 * @package App\Models
 * @property integer id
 * @property string name
 * @property string origin_name
 * @property integer country_id
 *
 * @OA\Schema(
 *      schema="City",
 *      required={
 *          "country_id",
 *          "name",
 *          "origin_name"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="country_id",
 *          type="integer",
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
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
class City extends BaseModel
{
    protected $table = 'cities';
}
