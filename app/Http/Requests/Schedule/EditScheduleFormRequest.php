<?php
declare(strict_types = 1);

namespace App\Http\Requests\Schedule;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ScheduleEditFormRequest
 * @package App\Http\Requests
 */
class EditScheduleFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'price_per_hour' => 'required|numeric|min:0',
            'currency' => 'required|currency',
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|uuid|exists:playgrounds,uuid'
        ];
    }
}
