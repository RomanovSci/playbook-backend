<?php

namespace App\Http\Requests\Common;

use App\Http\Requests\BaseFormRequest;

/**
 * Class TimeIntervalFormRequest
 * @package App\Http\Requests\Common
 */
class TimeIntervalFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
            'limit' => 'required|integer|max:100',
            'offset' => 'required|integer'
        ];
    }
}
