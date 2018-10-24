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
    const ROLE_PLAYGROUND_ADMIN = 'playground-admin';
    const ROLE_ADMIN = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
