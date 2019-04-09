<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\BaseFormRequest;

/**
 * Class SearchFormRequest
 * @package App\Http\Requests\Common
 */
class SearchFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return ['query' => 'required'];
    }
}
