<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TrainerInfo
 *
 * @package App\Models
 */
class TrainerInfo extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'trainers_info';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id', 'about', 'min_price',
        'max_price', 'currency'
    ];
}
