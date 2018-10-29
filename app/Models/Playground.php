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
 *
 * @property Organization organization
 */
class Playground extends Model
{
    use SoftDeletes;

    /**
     * @var string
     */
    protected $table = 'playgrounds';

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'address',
        'opening_time', 'closing_time', 'type_id',
        'organization_id',
    ];

    /**
     * @var array
     */
    protected $with = ['type'];

    /**
     * Get type
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo(PlaygroundType::class);
    }

    /**
     * Get organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
