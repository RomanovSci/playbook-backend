<?php

namespace App\Http\Requests\Playground;

use App\Http\Requests\BaseFormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests\Playground
 */
class Create extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'address' => 'required',
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s',
            'type_id' => 'required|numeric|exists:playground_types,id',
        ];
    }
}
