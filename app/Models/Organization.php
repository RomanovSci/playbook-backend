<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Organization
 *
 * @package App\Models
 * @property integer id
 * @property string name
 * @property integer owner_id
 * @property integer city_id
 */
class Organization extends Model
{
    use SoftDeletes;

    protected $table = 'organizations';

    protected $fillable = [
        'name', 'owner_id', 'city_id',
    ];
}
