<?php
declare(strict_types = 1);

namespace App\Http\Requests\TournamentPlayer;

use App\Http\Requests\BaseFormRequest;

/**
 * Class GetTournamentPlayerFormRequest
 * @package App\Http\Requests\TournamentPlayer
 */
class GetTournamentPlayerFormRequest extends BaseFormRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'tournament_uuid' => 'required|uuid|exists:tournaments,uuid',
        ];
    }
}
