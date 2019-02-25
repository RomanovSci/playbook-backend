<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ScheduleCreateFormRequest
 * @package App\Http\Requests
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
            'dates.*.start_time' => 'required|date_format:Y-m-d H:i:s',
            'dates.*.end_time' => 'required|date_format:Y-m-d H:i:s|after:dates.*.start_time',
            'price_per_hour' => 'required|numeric',
            'currency' => 'required|string|uppercase|currency',
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|numeric|exists:playgrounds,id'
        ];
    }
}
