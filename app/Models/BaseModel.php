<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

/**
 * Class BaseModel
 * @package App\Models
 *
 * @property string uuid
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @OA\Schema(
 *      @OA\Property(
 *          property="uuid",
 *          type="string"
 *      ),
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

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $hidden = [
        'deleted_at',
        'pivot',
    ];

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }
}
