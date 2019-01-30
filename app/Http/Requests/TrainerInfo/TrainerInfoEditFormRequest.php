<?php

namespace App\Http\Requests\TrainerInfo;

use App\Http\Requests\BaseFormRequest;

/**
 * Class TrainerInfoEditFormRequest
 * @package App\Http\Requests\TrainerInfo
 */
class TrainerInfoEditFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|exists:playgrounds,id',
            'about' => 'string',
            'min_price' => 'required|numeric',
            'max_price' => 'required|numeric|gte:min_price',
            'currency' => 'required|string|uppercase|currency',
            'image' => 'image|max:1024',
        ];
    }
}
