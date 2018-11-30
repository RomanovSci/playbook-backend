<?php

namespace App\Http\Requests\Schedule;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ScheduleGetFormRequest
 *
 * @package App\Http\Requests\Schedule
 */
class ScheduleGetFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ];
    }
}
