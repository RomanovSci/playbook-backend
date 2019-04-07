<?php

namespace App\Http\Requests\TrainerInfo;

use App\Http\Requests\BaseFormRequest;

/**
 * Class TrainerInfoCreateFormRequest
 * @package App\Http\Requests\TrainerInfo
 */
class CreateTrainerInfoFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|uuid|exists:playgrounds,uuid',
            'min_price' => 'required|integer|min:0',
            'max_price' => 'required|integer',
            'currency' => 'required|currency',
            'image' => 'image|max:1024',
        ];
    }
}
