<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlaygroundSchedule
 *
 * @package App
 *
 * @OA\Schema(
 *      schema="Schedule",
 *      required={
 *          "price_per_hour",
 *          "currency",
 *          "schedulable_id",
 *          "schedulable_type"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="start_time",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="end_time",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="price_per_hour",
 *          type="integer"
 *      ),
 *      @OA\Property(
 *          property="currency",
 *          type="string",
 *          minLength=3,
 *          maxLength=3
 *      ),
 *      @OA\Property(
 *          property="schedulable_id",
 *          type="integer",
 *          description="Reference to schedulable entity"
 *      ),
 *      @OA\Property(
 *          property="schedulable_type",
 *          type="string",
 *          description="Type of schedulable entity"
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
class Schedule extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'schedules';

    /**
     * @var array
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'price_per_hour',
        'currency',
        'schedulable_id',
        'schedulable_type',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'schedulable_id',
        'schedulable_type',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function schedulable()
    {
        return $this->morphTo();
    }
}
