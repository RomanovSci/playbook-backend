<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BaseModel
 * @package App\Models
 *
 * @OA\Schema(
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="deleted_at",
 *          type="string",
 *      )
 * )
 */
abstract class BaseModel extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
}
