<?php

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class VerifyPhoneFormRequest
 *
 * @package App\Http\Requests\User
 */
class VerifyPhoneFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return ['code' => 'required|digits:6'];
    }
}
