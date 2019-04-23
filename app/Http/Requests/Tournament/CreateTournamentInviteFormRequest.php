<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateTournamentInviteFormRequest
 * @package App\Http\Requests\Tournament
 */
class CreateTournamentInviteFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tournament_uuid' => 'required|uuid|exists:tournaments,uuid',
            'invited_uuid' => 'required|uuid|exists:users,uuid',
        ];
    }
}
