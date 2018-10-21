<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlaygroundType
 *
 * @package App\Models
 */
class PlaygroundType extends Model
{
    use SoftDeletes;

    protected $table = 'playground_types';
}
