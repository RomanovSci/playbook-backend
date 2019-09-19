<?php
declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Class Booking
 * @package App\Models
 *
 * @property string bookable_uuid
 * @property string bookable_type
 * @property string creator_uuid
 * @property Carbon start_time
 * @property Carbon end_time
 * @property integer status
 * @property string playground_uid
 * @property integer players_count
 * @property string note
 * @property integer price
 * @property string currency
 * @property string playground_uuid
 *
 * @property Schedule schedule
 * @property Playground|User bookable
 * @property User creator
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="creator_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="bookable_uuid",
 *                  description="string",
 *              ),
 *              @OA\Property(
 *                  property="bookable_type",
 *                  description="string",
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
 *              @OA\Property(
 *                  property="players_count",
 *                  type="integer",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Booking extends BaseModel
{
    public const STATUS_CREATED = 0;
    public const STATUS_CONFIRMED = 1;
    public const STATUS_DECLINED = 2;

    public const BOOKABLE_TYPES = [
        User::class => 'trainer',
        Playground::class => 'playground',
    ];

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
        'players_count',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s',
        'end_time' => 'datetime:Y-m-d H:i:s',
        'price' => 'integer',
        'status' => 'integer',
        'players_count' => 'integer',
    ];

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'bookable_type' => self::BOOKABLE_TYPES[$this->bookable_type] ?? $this->bookable_type
        ]);
    }

    /**
     * Bookable entities
     *
     * @return MorphTo
     */
    public function bookable(): MorphTo
    {
        return $this->morphTo(null, null, 'bookable_uuid');
    }

    /**
     * Creator entity
     *
     * @return BelongsTo
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Playground entity
     *
     * @return BelongsTo
     */
    public function playground(): BelongsTo
    {
        return $this->belongsTo(Playground::class);
    }

    /**
     * @return HasMany
     */
    public function equipmentsRent(): HasMany
    {
        return $this->hasMany(EquipmentRent::class);
    }
}
