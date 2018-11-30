<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ScheduleCreateFormRequest
 *
 * @package App\Http\Requests\Schedule
 */
class ScheduleCreateFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'dates' => 'required|array',
            'dates.*' => 'required|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i:s',
            'end_time' => 'required|date_format:H:i:s',
            'price_per_hour' => 'required|numeric',
            'currency' => 'required|string|uppercase|currency',
        ];
    }
}
