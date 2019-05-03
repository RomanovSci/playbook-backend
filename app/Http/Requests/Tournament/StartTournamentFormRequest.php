<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;

/**
 * Class StartTournamentFormRequest
 * @package App\Http\Requests\Tournament
 */
class StartTournamentFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'tournament_uuid' => 'required|uuid|exists:tournaments,uuid',
        ];
    }
}
