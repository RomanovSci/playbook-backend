<?php

namespace App\Http\Requests\TrainerInfo;

use App\Http\Requests\BaseFormRequest;

/**
 * Class TrainerInfoFormRequest
 * @package App\Http\Requests\TrainerInfo
 */
class TrainerInfoFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required|numeric|exists:users,id',
            'about' => 'string',
            'min_price' => 'required|numeric',
            'max_price' => 'required|numeric|gte:min_price',
            'currency' => 'required|string|uppercase|currency',
        ];
    }
}
