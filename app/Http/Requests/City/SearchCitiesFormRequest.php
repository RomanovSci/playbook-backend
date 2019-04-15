<?php

namespace App\Http\Requests\City;

use App\Http\Requests\Common\SearchFormRequest;

/**
 * Class SearchCitiesFormRequest
 * @package App\Http\Requests
 */
class SearchCitiesFormRequest extends SearchFormRequest
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
