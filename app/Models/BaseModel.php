<?php
declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;

/**
 * Class BaseModel
 * @package App\Models
 *
 * @property Uuid|string uuid
 * @property Carbon|string created_at
 * @property Carbon|string updated_at
 * @property Carbon|string deleted_at
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
    public static function boot(): void
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
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
