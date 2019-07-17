<?php
declare(strict_type = 1);

namespace App\Services\Tournament;
use App\Models\Tournament;
use App\Models\User;
use App\Services\ExecResult;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class CreateTournamentService
 * @package App\Services\Tournament
 */
class CreateTournamentService
{
    /**
     * @var ChallongeAPI
     */
    protected $challongeAPI;

    /**
     * CreateTournamentService constructor.
     *
     * @param ChallongeAPI $challongeAPI
     */
    public function __construct(ChallongeAPI $challongeAPI)
    {
        $this->challongeAPI = $challongeAPI;
    }

    /**
     * @param array $data
     * @return ExecResult
     */
    public function create(array $data): ExecResult
    {
        DB::beginTransaction();

        try {
            /**
             * @var User $user
             * @var Tournament $tournament
             */
            $user = Auth::user();
            $tournament = Tournament::create(array_merge($data, ['creator_uuid' => $user->uuid]));
            $challongeOptions = [
                'tournament' => [
                    'name' => $data['name'],
                    'description' => $data['description'] ?? '',
                    'url' => str_replace('-', '_', (string) $tournament->uuid),
                    'game_name' => 'Tennis',
                    'private' => $data['is_private'],
                ],
            ];

            if (isset($data['max_participants_count'])) {
                $challongeOptions['tournament']['signup_cap'] = $data['max_participants_count'];
            }

            if (isset($data['start_time'])) {
                $challongeOptions['tournament']['start_at'] = $data['start_time'];

            }

            $challongeCreateResult = $this->challongeAPI->createTournament($challongeOptions);

            if ($challongeCreateResult['status_code'] !== 200) {
                throw new \Exception(
                    "Can't create challonge tournament. Reason: " . $challongeCreateResult['errors'][0]
                );
            }

            $tournament->challonge_id = $challongeCreateResult['data']->tournament->id;

            if (!$tournament->update(['challonge_id'])) {
                throw new \Exception("Can't update tournament");
            }

            DB::commit();
            return ExecResult::instance()->setSuccess()->setData($tournament->toArray());
        } catch (\Throwable $e) {
            DB::rollBack();
            return ExecResult::instance()->setMessage($e->getMessage());
        }
    }
}
