<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\CreateTournamentInviteFormRequest;

/**
 * Class TournamentInviteController
 * @package App\Http\Controllers\API
 */
class TournamentInviteController extends Controller
{
    public function create(CreateTournamentInviteFormRequest $request)
    {
        return $this->success();
    }
}
