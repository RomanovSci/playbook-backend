<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\BaseFormRequest;

/**
 * Class GetFormRequest
 * @package App\Http\Requests\Common
 */
class GetFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'limit' => 'required|integer|max:100',
            'offset' => 'required|integer'
        ];
    }
}
