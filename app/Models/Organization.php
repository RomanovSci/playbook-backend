<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Organization
 *
 * @package App\Models
 */
class Organization extends Model
{
    protected $table = 'organization';

    protected $fillable = [
        'name', 'owner_id',
    ];
}
