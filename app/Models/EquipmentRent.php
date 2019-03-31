<?php

namespace App\Models;

/**
 * Class EquipmentRent
 * @package App\Models
 *
 * @property string booking_uuid
 * @property string equipment_uuid
 * @property integer count
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
}
