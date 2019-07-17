<?php
declare(strict_types = 1);

namespace App\Http\Requests\Booking;

use App\Http\Requests\BaseFormRequest;

/**
 * Class BookingDeclineFormRequest
 * @package App\Http\Requests\Booking
 */
class DeclineBookingFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return ['note' => 'required||max:255'];
    }
}
