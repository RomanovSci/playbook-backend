<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 *      schema="Organization",
 *      required={
 *          "owner_id",
 *          "name",
 *          "city_id"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="owner_id",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="city_id",
 *          type="integer",
 *          format="int32"
 *     ),
 *     @OA\Property(
 *          property="created_at",
 *          type="string",
 *          readOnly=true
 *     ),
 *     @OA\Property(
 *          property="updated_at",
 *          type="integer",
 *          readOnly=true
 *     ),
 *     @OA\Property(
 *          property="deleted_at",
 *          type="string",
 *          readOnly=true
 *     )
 * )
 */
class Organization extends Model
{
    use SoftDeletes;

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
