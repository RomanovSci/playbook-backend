<?php

namespace App\Http\Requests\PlaygroundSchedule;

use App\Http\Requests\BaseFormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests\PlaygroundSchedule
 */
class Create extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'price_per_hour' => 'required|numeric',
            'currency' => 'required|string|uppercase|currency',
        ];
    }
}
