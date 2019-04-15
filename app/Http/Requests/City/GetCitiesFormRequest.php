<?php

namespace App\Http\Requests\City;

use App\Http\Requests\Common\GetFormRequest;

/**
 * Class GetCitiesFormRequest
 * @package App\Http\Requests
 */
class GetCitiesFormRequest extends GetFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'country_uuid' => 'uuid|exists:countries,uuid',
        ]);
    }
}
