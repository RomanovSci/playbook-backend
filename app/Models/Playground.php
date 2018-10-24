<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Playground
 *
 * @package App
 */
class Playground extends Model
{
    use SoftDeletes;

    protected $table = 'playgrounds';

    protected $fillable = [
        'name', 'description', 'address',
        'opening_time', 'closing_time', 'type_id',
    ];
}
