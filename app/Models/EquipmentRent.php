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
}
