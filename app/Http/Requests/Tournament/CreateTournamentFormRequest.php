<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateTournamentFormRequest
 * @package App\Http\Requests\Tournament
 */
class CreateTournamentFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'string|max:255',
            'tournament_type_uuid' => 'required|uuid|exists:tournaments_types,uuid',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
        ];
    }
}
