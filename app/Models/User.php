<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 * @package App\Models
 *
 * @property integer id
 * @property integer timezone_id
 * @property string first_name
 * @property string last_name
 * @property integer phone
 * @property string password
 * @property integer verification_code
 * @property string phone_verified_at
 * @property string created_at
 * @property string updated_at
 * @property string deleted_at
 *
 * @property Playground[] $playgrounds
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
 *                  property="id",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="timezone_id",
 *                  type="integer",
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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'timezone_id',
        'first_name',
        'last_name',
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
        'password',
        'verification_code',
        'roles',
        'deleted_at',
    ];

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
        return $this->morphMany(Schedule::class, 'schedulable');
    }

    /**
     * Get trainer bookings
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function trainerBookings()
    {
        return $this->morphMany(Booking::class, 'bookable');
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
