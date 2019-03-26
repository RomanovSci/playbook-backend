<?php

namespace App\Models;

use App\Models\Schedule\Schedule;

/**
 * Class Playground
 * @package App\Models
 *
 * @property string name
 * @property string description
 * @property string address
 * @property \DateTime opening_time
 * @property \DateTime closing_time
 * @property string type_uuid
 * @property string organization_uuid
 * @property string creator_uuid
 * @property Organization organization
 * @property User creator
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="description",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="address",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="opening_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="closing_time",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="status",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="type",
 *                  type="object",
 *                  ref="#/components/schemas/PlaygroundType"
 *              ),
 *              @OA\Property(
 *                  property="organization",
 *                  type="object",
 *                  ref="#/components/schemas/Organization"
 *              ),
 *              @OA\Property(
 *                  property="creator",
 *                  type="object",
 *                  ref="#/components/schemas/User"
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Playground extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'playgrounds';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'address',
        'opening_time',
        'closing_time',
        'type_uuid',
        'organization_uuid',
        'creator_uuid',
    ];

    /**
     * @var array
     */
    protected $with = [
        'organization',
        'type',
        'creator',
        'schedules',
    ];

    /**
     * Playground constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, [
            'organization_uuid',
            'type_uuid',
            'creator_uuid',
        ]);
    }

    /**
     * Get type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(PlaygroundType::class);
    }

    /**
     * Get creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get schedules
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function schedules()
    {
        return $this->morphMany(Schedule::class, 'schedulable', null, 'schedulable_uuid');
    }

    /**
     * Get bookings
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookings()
    {
        return $this->morphMany(Booking::class, 'schedulable', null, 'schedulable_uuid');
    }
}
