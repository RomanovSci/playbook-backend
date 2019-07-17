<?php
declare(strict_types = 1);

namespace App\Http\Requests\Organization;

use App\Http\Requests\BaseFormRequest;

/**
 * Class OrganizationCreateFormRequest
 * @package App\Http\Requests\Organization
 */
class CreateOrganizationFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255',
            'city_uuid' => 'required|uuid|exists:cities,uuid',
        ];
    }
}
