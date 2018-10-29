<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlaygroundSchedule
 *
 * @package App
 */
class PlaygroundSchedule extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'schedules';

    /**
     * @var array
     */
    protected $fillable = [
        'start_time', 'end_time',
        'price_per_hour', 'currency',
    ];
}
