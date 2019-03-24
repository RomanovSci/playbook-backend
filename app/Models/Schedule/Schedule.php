<?php

namespace App\Models\Schedule;

use App\Models\BaseModel;
use App\Models\Playground;
use App\Models\User;
use Carbon\Carbon;

/**
 * Class PlaygroundSchedule
 * @package App\Models
 *
 * @property Carbon start_time
 * @property Carbon end_time
 * @property integer price_per_hour
 * @property string currency
 * @property string schedulable_uuid
 * @property string schedulable_type
 *
 * @property Playground|User $schedulable
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="uuid",
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
 *                  property="price_per_hour",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *                  minLength=3,
 *                  maxLength=3
 *              ),
 *              @OA\Property(
 *                  type="array",
 *                  property="playgrounds",
 *                  @OA\Items(ref="#/components/schemas/Playground")
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Schedule extends BaseModel
{
    const SCHEDULE_TYPES = [
        'trainer' => User::class,
        'playground' => Playground::class,
    ];

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
        'schedulable_uuid',
        'schedulable_type',
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
        'playgrounds',
    ];

    /**
     * Schedule constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, [
            'schedulable_uuid',
            'schedulable_type',
        ]);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function schedulable()
    {
        return $this->morphTo(null, null, 'schedulable_uuid');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function playgrounds()
    {
        return $this->belongsToMany(Playground::class, 'schedules_playgrounds');
    }
}
