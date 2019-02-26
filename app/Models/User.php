<?php

namespace App\Models;

use App\Models\Schedule\Schedule;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 *
 * @property string uuid
 * @property string timezone_uuid
 * @property string city_uuid
 * @property string language_uuid
 * @property string first_name
 * @property string last_name
 * @property string middle_name
 * @property integer phone
 * @property string password
 * @property integer verification_code
 * @property string phone_verified_at
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @property Playground[] $playgrounds
 * @property TrainerInfo $trainerInfo
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              required={
 *                  "first_name",
 *                  "last_name",
 *                  "phone",
 *                  "password"
 *              },
 *              @OA\Property(
 *                  property="uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="timezone_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="city_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="language_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="first_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="last_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="middle_name",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="phone",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="password",
 *                  description="hidden",
 *              ),
 *              @OA\Property(
 *                  property="phone_verified_at",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="verification_code",
 *                  description="hidden",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles, SoftDeletes;

    const ROLE_USER = 'user';
    const ROLE_TRAINER = 'trainer';
    const ROLE_ORGANIZATION_ADMIN = 'organization-admin';
    const ROLE_ADMIN = 'admin';

    /**
     * @var string
     */
    protected $guard_name = 'api';

    /**
     * @var string
     */
    protected $primaryKey = 'uuid';

    /**
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timezone_uuid',
        'city_uuid',
        'first_name',
        'last_name',
        'middle_name',
        'phone',
        'password',
        'verification_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id',
        'password',
        'verification_code',
        'roles',
        'deleted_at',
    ];

    /**
     * @inheritdoc
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->uuid = Uuid::uuid4();
        });
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'uuid';
    }

    /**
     * @param $username
     * @return mixed
     */
    public function findForPassport($username)
    {
        return $this->where('phone', $username)->first();
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
     * Get trainer info
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function trainerInfo()
    {
        return $this->hasOne(TrainerInfo::class);
    }

    /**
     * Get trainer bookings
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function trainerBookings()
    {
        return $this->morphMany(Booking::class, 'bookable', null, 'bookable_uuid');
    }

    /**
     * Get playgrounds
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function playgrounds()
    {
        return $this->belongsToMany(Playground::class, 'users_playgrounds');
    }
}
