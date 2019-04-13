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
            'name' => 'required|max:255',
            'tournament_type_uuid' => 'required|uuid|exists:tournaments_types,uuid',
        ];
    }
}
