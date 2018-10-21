<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlaygroundRentPrice
 *
 * @package App
 */
class PlaygroundRentPrice extends Model
{
    use SoftDeletes;

    protected $table = 'playground_rent_prices';
}
