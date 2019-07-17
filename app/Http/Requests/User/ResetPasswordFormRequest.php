<?php
declare(strict_types = 1);

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ResetPasswordFormRequest
 * @package App\Http\Requests\User
 */
class ResetPasswordFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return ['phone' => 'required|numeric|digits_between:9,12|exists:users,phone'];
    }
}
