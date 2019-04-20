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
     * @inheritDoc
     */
    protected function prepareForValidation()
    {
        $this->replace(array_merge($this->all(), [
            'min_price' => (string) (int) $this->input('min_price'),
            'max_price' => (string) (int) $this->input('max_price'),
        ]));
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'playgrounds' => 'required|array',
            'playgrounds.*' => 'required|uuid|exists:playgrounds,uuid',
            'min_price' => 'required|integer|min:0',
            'max_price' => 'required|integer|gte:min_price',
            'currency' => 'required|currency',
            'image' => 'image|max:1024',
        ];
    }
}
