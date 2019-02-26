<?php

namespace App\Http\Requests\Playground;

use App\Http\Requests\BaseFormRequest;

/**
 * Class PlaygroundCreateFormRequest
 * @package App\Http\Requests\Playground
 */
class PlaygroundCreateFormRequest extends BaseFormRequest
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
            'type_uuid' => 'required|uuid|exists:playground_types,uuid',
        ];
    }
}
