<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateUser
 * @package App\Http\Requests\User
 */
class LoginFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|numeric',
            'password' => 'required',
        ];
    }
}
