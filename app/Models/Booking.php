<?php

namespace App\Models;

use App\Models\Schedule\Schedule;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Booking
 * @package App\Models
 *
 * @property integer id
 * @property integer bookable_id
 * @property integer bookable_type
 * @property integer creator_id
 * @property string start_time
 * @property string end_time
 * @property integer status
 * @property integer playground_id
 * @property string note
 * @property integer price
 * @property string currency
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @property Schedule schedule
 * @property Playground|User $bookable
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
 *                  description="hidden",
 *              ),
 *              @OA\Property(
 *                  property="bookable_type",
 *                  description="hidden",
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
 *                  property="note",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="price",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="playground_id",
 *                  type="integer",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Booking extends BaseModel
{
    const STATUS_CREATED = 0;
    const STATUS_CONFIRMED = 1;
    const STATUS_DECLINED = 2;

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
        'note',
        'price',
        'currency',
        'status',
        'playground_id',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s P',
        'end_time' => 'datetime:Y-m-d H:i:s P',
        'bookable_id' => 'integer',
        'creator_id' => 'integer',
        'playground_id' => 'integer',
    ];

    /**
     * @var array
     */
    protected $with = [
        'bookable',
    ];

    /**
     * Booking constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, [
            'bookable_id',
            'bookable_type',
        ]);
    }

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

    /**
     * Playground entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playground()
    {
        return $this->belongsTo(Playground::class);
    }
}
