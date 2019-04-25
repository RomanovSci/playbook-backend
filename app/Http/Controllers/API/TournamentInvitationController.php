<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tournament\CreateTournamentInvitationFormRequest;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Class TournamentInviteController
 * @package App\Http\Controllers\API
 */
class TournamentInvitationController extends Controller
{
    public function create(CreateTournamentInvitationFormRequest $request)
    {
        /**
         * @var User $user
         * @var TournamentInvitation $tournamentInvitation
         */
        $user = Auth::user();
        $tournamentInvitation = TournamentInvitation::create(array_merge($request->all(), [
            'inviter_uuid' => $user->uuid,
        ]));

        return $this->success($tournamentInvitation->toArray());
    }
}
