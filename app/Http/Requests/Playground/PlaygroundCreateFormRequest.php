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
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'description' => 'required|max:255',
            'address' => 'required|max:255',
            'opening_time' => 'required|date_format:H:i:s',
            'closing_time' => 'required|date_format:H:i:s',
            'type_uuid' => 'uuid|exists:playground_types,uuid',
            'organization_uuid' => 'uuid|exists:organizations,uuid',
        ];
    }
}
