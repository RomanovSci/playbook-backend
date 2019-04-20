<?php

namespace App\Http\Requests\Common;

/**
 * Class SearchFormRequest
 * @package App\Http\Requests\Common
 */
class SearchFormRequest extends GetFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'query' => 'required'
        ]);
    }
}
