<?php

namespace App\Http\Requests\Organization;

use App\Http\Requests\BaseFormRequest;

/**
 * Class Create
 *
 * @package App\Http\Requests\Organization
 */
class Create extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'owner_id' => 'required|exists:users,id',
        ];
    }
}
