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
    public function rules(): array
    {
        return [
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|uuid|exists:playgrounds,uuid',
            'min_price' => 'required|numeric|min:0',
            'max_price' => 'required|numeric|gte:min_price',
            'currency' => 'required|currency',
            'image' => 'image|max:1024',
        ];
    }
}
