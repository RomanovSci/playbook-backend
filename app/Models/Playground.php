<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Playground
 *
 * @package App\Models
 * @property string name
 * @property string description
 * @property string address
 * @property \DateTime opening_time
 * @property \DateTime closing_time
 * @property integer type_id
 * @property integer organization_id
 */
class Playground extends Model
{
    use SoftDeletes;

    protected $table = 'playgrounds';

    protected $fillable = [
        'name', 'description', 'address',
        'opening_time', 'closing_time', 'type_id',
        'organization_id',
    ];
}
