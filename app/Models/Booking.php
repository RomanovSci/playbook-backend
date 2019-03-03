<?php

namespace App\Models;

use App\Models\Schedule\Schedule;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Booking
 * @package App\Models
 *
 * @property string bookable_uuid
 * @property integer bookable_type
 * @property string creator_uuid
 * @property string start_time
 * @property string end_time
 * @property integer status
 * @property string playground_uid
 * @property string note
 * @property integer price
 * @property string currency
 *
 * @property Schedule schedule
 * @property Playground|User $bookable
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "start_time",
 *                  "end_time"
 *              },
 *              @OA\Property(
 *                  property="creator_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="bookable_uuid",
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
 *                  property="playground_uuid",
 *                  type="string",
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
        'bookable_uuid',
        'bookable_type',
        'creator_uuid',
        'start_time',
        'end_time',
        'note',
        'price',
        'currency',
        'status',
        'playground_uuid',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s P',
        'end_time' => 'datetime:Y-m-d H:i:s P',
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
            'bookable_uuid',
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
        return $this->morphTo(null, null, 'bookable_uuid');
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
