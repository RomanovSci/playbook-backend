<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;

/**
 * Class BookingCreateFormRequest
 * @package App\Http\Requests\Booking
 */
class BookingCreateFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'bookable_id' => 'required|bookable_exists',
            'playground_id' => 'numeric|exists:playgrounds,id',
        ];
    }
}
