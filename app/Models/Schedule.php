<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PlaygroundSchedule
 *
 * @package App
 */
class Schedule extends Model
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

    /**
     * Get playgrounds
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function playgrounds()
    {
        return $this->morphedByMany(
            Playground::class,
            'entity',
            'schedules_to_entities'
        );
    }

    /**
     * Users
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function users()
    {
        return $this->morphedByMany(
            User::class,
            'entity',
            'schedules_to_entities'
        );
    }
}
