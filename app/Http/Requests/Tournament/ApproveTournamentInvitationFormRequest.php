<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ApproveTournamentInvitationFormRequest
 * @package App\Http\Requests\Tournament
 */
class ApproveTournamentInvitationFormRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'tournament_invitation_uuid' => 'required|uuid|exists:tournaments_invitations,uuid',
        ];
    }
}
