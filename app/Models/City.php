<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class City
 *
 * @package App\Models
 * @property string name
 * @property string origin_name
 * @property integer country_id
 */
class City extends Model
{
    use SoftDeletes;

    protected $table = 'cities';
}
