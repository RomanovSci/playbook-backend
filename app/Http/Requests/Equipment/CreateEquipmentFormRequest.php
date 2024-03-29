<?php
declare(strict_types = 1);

namespace App\Http\Requests\Equipment;

use App\Http\Requests\BaseFormRequest;

/**
 * Class EquipmentCreateFormRequest
 * @package App\Http\Requests
 */
class CreateEquipmentFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric|min:0',
            'currency' => 'required|currency',
            'availability' => 'required|numeric|min:1',
        ];
    }
}
