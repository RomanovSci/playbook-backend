<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\CreateTournamentFormRequest;

/**
 * Class TournamentController
 * @package App\Http\Controllers\API
 */
class TournamentController extends Controller
{
    /**
     * @param CreateTournamentFormRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateTournamentFormRequest $request)
    {
        return $this->success();
    }
}
