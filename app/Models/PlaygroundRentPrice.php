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

    /**
     * @var string
     */
    protected $table = 'playground_rent_prices';

    /**
     * Get playground
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function playground()
    {
        return $this->belongsTo(Playground::class);
    }
}
