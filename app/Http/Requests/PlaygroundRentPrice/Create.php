<?php

namespace App\Http\Requests\PlaygroundRentPrice;

use App\Http\Requests\BaseFormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests\PlaygroundRentPrice
 */
class Create extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'playground_id' => 'required|numeric|exists:playgrounds,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date',
            'price_per_hour' => 'required|numeric',
            'currency' => 'required|string|uppercase|currency',
        ];
    }
}
