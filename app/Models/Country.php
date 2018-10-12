<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Country
 *
 * @package App\Models
 * @property string code
 * @property string name
 */
class Country extends Model
{
    use SoftDeletes;

    protected $table = 'countries';
}
