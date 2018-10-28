<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlaygroundType
 *
 * @package App\Models
 * @property string type
 */
class PlaygroundType extends Model
{
    use SoftDeletes;

    protected $table = 'playgrounds_types';
}
