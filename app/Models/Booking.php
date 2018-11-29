<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Booking
 *
 * @package App\Models
 * @property integer id
 * @property integer schedule_id
 * @property string start_time
 * @property string end_time
 * @property integer status
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @property Schedule schedule
 *
 * @OA\Schema(
 *      schema="Booking",
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "schedule_id",
 *                  "start_time",
 *                  "end_time"
 *              },
 *              @OA\Property(
 *                  property="id",
 *                  type="integer",
 *                  readOnly=true
 *              ),
 *              @OA\Property(
 *                  property="schedule_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="start_time",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="end_time",
 *                  type="string"
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="integer"
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Booking extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'bookings';

    /**
     * @var array
     */
    protected $fillable = [
        'schedule_id',
        'start_time',
        'end_time',
        'status,'
    ];

    /**
     * @var array
     */
    protected $with = [
        'schedule'
    ];

    /**
     * Bookable entities
     *
     * @return BelongsTo
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
