<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateUser
 *
 * @package App\Http\Requests\User
 */
class UserCreateFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|min:9|unique:users,phone',
            'password' => 'min:3',
            'c_password' => 'required_with:password|same:password',
            'is_trainer' => 'required|boolean',
        ];
    }
}
