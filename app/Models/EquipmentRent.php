<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class EquipmentRent
 * @package App\Models
 *
 * @property string booking_uuid
 * @property string equipment_uuid
 * @property integer count
 *
 * @property Equipment equipment
 */
class EquipmentRent extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'equipments_rent';

    /**
     * @var array
     */
    protected $casts = [
        'count' => 'integer',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'booking_uuid',
        'equipment_uuid',
        'count',
    ];

    /**
     * EquipmentRent constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->hidden = array_merge($this->hidden, [
            'uuid',
            'booking_uuid',
            'equipment_uuid',
            'created_at',
            'updated_at',
        ]);
    }

    /**
     * @return BelongsTo
     */
    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
