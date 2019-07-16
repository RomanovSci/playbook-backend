<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class TournamentMatch
 * @package App\Models
 *
 * @property string tournament_uuid
 * @property string first_participant_uuid
 * @property string second_participant_uuid
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="tournament_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="first_participant_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="second_participant_uuid",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class TournamentMatch extends BaseModel
{
    /**
     * @var array
     */
    protected $casts = [
        'challonge_id' => 'integer',
    ];

    /**
     * @var string
     */
    protected $table = 'tournaments_matches';
}
