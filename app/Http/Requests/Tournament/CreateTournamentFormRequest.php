<?php
declare(strict_types = 1);

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
            'title' => 'required|string',
            'description' => 'string',
            'sport' => 'required|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|currency',
            'tournament_type_uuid' => 'required|uuid|exists:tournament_types,uuid',
            'third_place_match' => 'boolean',
            'players_count_in_group' => 'numeric|min:0',
            'players_count_in_playoff' => 'numeric|min:0',
            'metadata' => 'json',
            'state' => 'json',
            'started_at' => 'date_format:Y-m-d H:i:s',
            'players' => 'required|array',
            'players.*.uuid' => 'uuid|exists:users,uuid',
            'players.*.first_name' => 'required|max:255',
            'players.*.last_name' => 'required|max:255',
        ];
    }
}
