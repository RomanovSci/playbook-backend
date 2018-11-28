<?php

namespace App\Models;

/**
 * Class PlaygroundSchedule
 *
 * @package App
 *
 * @OA\Schema(
 *      schema="Schedule",
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "price_per_hour",
 *                  "currency",
 *                  "schedulable_id",
 *                  "schedulable_type"
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  readOnly=true
 *              ),
 *              @OA\Property(
 *                  property="start_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="end_time",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="price_per_hour",
 *                  type="integer"
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *                  minLength=3,
 *               maxLength=3
 *              ),
 *              @OA\Property(
 *                  property="schedulable_id",
 *                  type="integer",
 *                  description="Reference to schedulable entity"
 *              ),
 *              @OA\Property(
 *                  property="schedulable_type",
 *                  type="string",
 *                  description="Type of schedulable entity"
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Schedule extends BaseModel
{
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
