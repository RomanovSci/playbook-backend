<?php

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;

/**
 * Class BookingConfirmFormRequest
 *
 * @package App\Http\Requests\Booking
 */
class BookingConfirmFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'booking_id' => 'required|numeric|exists:bookings,id',
        ];
    }
}
