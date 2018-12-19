<?php

namespace App\Http\Requests\Playground;

use App\Http\Requests\BaseFormRequest;

/**
 * Class PlaygroundSearchFormRequest
 * @package App\Http\Requests\Playground
 */
class PlaygroundSearchFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        return ['query' => 'required'];
    }
}
