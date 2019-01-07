<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BaseModel
 * @package App\Models
 *
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @OA\Schema(
 *      @OA\Property(
 *          property="created_at",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="deleted_at",
 *          description="hidden",
 *      )
 * )
 */
abstract class BaseModel extends Model
{
    use SoftDeletes;

    protected $hidden = [
        'deleted_at',
    ];

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
}