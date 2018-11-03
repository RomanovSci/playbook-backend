<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Country
 *
 * @package App\Models
 * @property string code
 * @property string name
 * @property string origin_name
 *
 * @OA\Schema(
 *      schema="Country",
 *      required={
 *          "code",
 *          "name",
 *          "origin_name"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="origin_name",
 *          type="string"
 *      ),
 *     @OA\Property(
 *          property="created_at",
 *          type="string",
 *          readOnly=true
 *     ),
 *     @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          readOnly=true
 *     ),
 *     @OA\Property(
 *          property="deleted_at",
 *          type="string",
 *          readOnly=true
 *     )
 * )
 */
class Country extends Model
{
    use SoftDeletes;

    protected $table = 'countries';
}
