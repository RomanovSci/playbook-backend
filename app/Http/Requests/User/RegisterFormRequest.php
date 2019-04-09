<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateUser
 * @package App\Http\Requests\User
 */
class RegisterFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'middle_name' => 'max:255',
            'phone' => 'required|numeric|digits_between:9,12|unique:users,phone',
            'password' => 'min:3',
            'c_password' => 'required_with:password|same:password',
            'is_trainer' => 'required|boolean',
        ];
    }
}
