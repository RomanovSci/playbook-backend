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
            'is_private' => 'required|boolean',
            'start_time' => 'date_format:Y-m-d H:i:s',
            'end_time' => 'date_format:Y-m-d H:i:s|after:start_time',
            'registration_start_time' => 'date_format:Y-m-d H:i:s|after:start_time',
            'max_participants_count' => 'integer|min:1',
            'price' => 'integer|min:0',
            'currency' => 'currency',
            'tournament_type_uuid' => 'required|uuid|exists:tournaments_types,uuid',
            'tournament_grid_type_uuid' => 'required|uuid|exists:tournaments_grids_types,uuid',
        ];
    }
}
