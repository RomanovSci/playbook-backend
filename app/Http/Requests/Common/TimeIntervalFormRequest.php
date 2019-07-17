<?php
declare(strict_types = 1);

namespace App\Http\Requests\Common;

/**
 * Class TimeIntervalFormRequest
 * @package App\Http\Requests\Common
 */
class TimeIntervalFormRequest extends GetFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'start_time' => 'required|date_format:Y-m-d H:i:s',
            'end_time' => 'required|date_format:Y-m-d H:i:s|after:start_time',
        ]);
    }
}
