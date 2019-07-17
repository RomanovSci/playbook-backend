<?php
declare(strict_types = 1);

namespace App\Http\Requests\Tournament;

use App\Http\Requests\BaseFormRequest;

/**
 * Class CreateTournamentRequestFormRequest
 * @package App\Http\Requests\Tournament
 */
class CreateTournamentRequestFormRequest extends BaseFormRequest
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
