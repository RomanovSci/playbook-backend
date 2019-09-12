<?php
declare(strict_types = 1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
 * @property integer status
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
 *                  property="type_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="organization_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="creator_uuid",
 *                  type="string",
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
     * Get type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(PlaygroundType::class);
    }

    /**
     * Get creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get schedules
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function schedules(): MorphMany
    {
        return $this->morphMany(Schedule::class, 'schedulable', null, 'schedulable_uuid');
    }

    /**
     * Get bookings
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'schedulable', null, 'schedulable_uuid');
    }
}
