<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 *          "id",
 *          "country_id",
 *          "name",
 *          "origin_name"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="country_id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *     @OA\Property(
 *          property="created_at",
 *          type="integer",
 *          format="int32"
 *     ),
 *     @OA\Property(
 *          property="updated_at",
 *          type="integer",
 *          format="int32"
 *     ),
 *     @OA\Property(
 *          property="deleted_at",
 *          type="integer",
 *          format="int32"
 *     )
 * )
 */
class City extends Model
{
    use SoftDeletes;

    protected $table = 'cities';
}
