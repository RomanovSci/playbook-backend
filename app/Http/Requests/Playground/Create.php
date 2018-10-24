<?php

namespace App\Http\Requests\Playground;

use App\Http\Requests\BaseFormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests
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
            'opening_time' => 'required|date',
            'closing_time' => 'required|date',
            'type_id' => 'required|exists:playground_types,id',
        ];
    }
}
