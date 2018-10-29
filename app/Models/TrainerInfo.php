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

    protected $table = 'trainers_info';
}
