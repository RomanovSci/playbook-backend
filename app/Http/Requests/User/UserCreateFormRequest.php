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
            'password' => 'required',
            'c_password' => 'required|same:password',
            'is_trainer' => 'required|boolean',
        ];
    }
}
