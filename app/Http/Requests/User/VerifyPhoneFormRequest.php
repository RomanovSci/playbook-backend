<?php
declare(strict_types = 1);

namespace App\Http\Requests\User;

use App\Http\Requests\BaseFormRequest;

/**
 * Class VerifyPhoneFormRequest
 * @package App\Http\Requests\User
 */
class VerifyPhoneFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return ['code' => 'required'];
    }
}
