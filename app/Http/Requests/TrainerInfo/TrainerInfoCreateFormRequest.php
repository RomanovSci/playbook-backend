<?php

namespace App\Http\Requests\TrainerInfo;

use App\Http\Requests\BaseFormRequest;

/**
 * Class TrainerInfoCreateFormRequest
 * @package App\Http\Requests\TrainerInfo
 */
class TrainerInfoCreateFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|exists:playgrounds,uuid',
            'about' => 'string',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|gte:min_price',
            'currency' => 'required|string|uppercase|currency',
            'image' => 'image|max:1024',
        ];
    }
}
