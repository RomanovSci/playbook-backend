<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ScheduleEditFormRequest
 * @package App\Http\Requests
 */
class ScheduleEditFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'price_per_hour' => 'required|numeric',
            'currency' => 'required|string|uppercase|currency',
        ];
    }
}
