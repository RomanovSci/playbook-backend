<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class SmsDelivery
 * @package App\Models
 *
 * @property string phone
 * @property string text
 * @property string data
 * @property boolean success
 */
class SmsDelivery extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'sms_deliveries';

    /**
     * @var array
     */
    protected $fillable = [
        'phone',
        'text',
        'data',
        'success',
    ];
}
