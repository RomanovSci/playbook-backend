<?php

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;

/**
 * Class ApproveTournamentRequestFormRequest
 * @package App\Http\Requests\Tournament
 */
class ApproveTournamentRequestFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tournament_request_uuid' => 'required|uuid|exists:tournaments_requests,uuid',
        ];
    }
}
