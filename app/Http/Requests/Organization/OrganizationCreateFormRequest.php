<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\BaseFormRequest;

/**
 * Class OrganizationCreateFormRequest
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
            'name' => 'required|max:255',
            'city_uuid' => 'required|uuid|exists:cities,uuid',
        ];
    }
}
