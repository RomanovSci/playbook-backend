<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @package App\Models
 * @property integer id
 * @property string first_name
 * @property string last_name
 * @property integer phone
 * @property string password
 *
 * @OA\Schema(
 *      schema="User",
 *      required={
 *          "first_name",
 *          "last_name",
 *          "phone",
 *          "password"
 *      },
 *      @OA\Property(
 *          property="id",
 *          type="integer",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="first_name",
 *          type="string",
 *      ),
 *      @OA\Property(
 *          property="last_name",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="phone",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="password",
 *          type="string"
 *      ),
 *      @OA\Property(
 *          property="phone_verified_at",
 *          type="string",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="created_at",
 *          type="string",
 *          readOnly=true
 *      ),
 *      @OA\Property(
 *          property="updated_at",
 *          type="string",
 *          readOnly=true
 *     ),
 *     @OA\Property(
 *          property="deleted_at",
 *          type="string",
 *          readOnly=true
 *     )
 * )
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    const ROLE_USER = 'user';
    const ROLE_TRAINER = 'trainer';
    const ROLE_ORGANIZATION_ADMIN = 'organization-admin';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password'];

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
        return $this->morphMany(Schedule::class,'schedulable');
    }
}
