<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Booking
 * @package App\Models
 *
 * @property integer id
 * @property integer bookable_id
 * @property integer bookable_type
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
 *              ),
 *              @OA\Property(
 *                  property="creator_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="bookable_id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="bookable_type",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="start_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="end_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="integer",
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
        'bookable_id',
        'bookable_type',
        'creator_id',
        'start_time',
        'end_time',
        'status,'
    ];

    /**
     * Bookable entities
     *
     * @return MorphTo
     */
    public function bookable()
    {
        return $this->morphTo();
    }

    /**
     * Creator entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class);
    }
}
