<?php

namespace App\Http\Requests\Playground;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests
 */
class Create extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
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
