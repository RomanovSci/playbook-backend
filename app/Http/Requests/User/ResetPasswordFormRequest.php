<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ResetPasswordFormRequest
 * @package App\Http\Requests\User
 */
class ResetPasswordFormRequest extends BaseFormRequest
{
    public function rules()
    {
        return ['phone' => 'required|numeric|digits_between:min:9,12|exists:users,phone'];
    }
}
