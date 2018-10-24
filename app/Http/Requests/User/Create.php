<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateUser
 *
 * @package App\Http\Requests\User
 */
class Create extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'phone' => 'required|numeric|min:10',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ];
    }
}
