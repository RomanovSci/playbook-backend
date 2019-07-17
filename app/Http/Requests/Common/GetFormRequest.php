<?php
declare(strict_types = 1);

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
            'query' => 'string|max:100',
            'limit' => 'required|integer|max:100',
            'offset' => 'required|integer'
        ];
    }
}
