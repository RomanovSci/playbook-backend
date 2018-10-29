<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\BaseFormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests\Organization
 */
class OrganizationCreateFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'city_id' => 'required|numeric|exists:cities,id',
        ];
    }
}
