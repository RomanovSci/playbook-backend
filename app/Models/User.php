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
 */
class User extends Authenticatable
{
    use Notifiable, HasApiTokens, HasRoles;

    const ROLE_USER = 'user';
    const ROLE_COACH = 'coach';
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
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function schedules()
    {
        return $this->morphToMany(
            Schedule::class,
            'entity',
            'schedules_to_entities'
        );
    }
}
