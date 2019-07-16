<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class TournamentParticipant
 * @package App\Models
 *
 * @property string nickname
 * @property string user_uuid
 * @property string tournament_uuid
 * @property integer challonge_id
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="nickname",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="user_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="tournament_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="challonge_id",
 *                  type="integer",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class TournamentParticipant extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_participants';

    /**
     * @var array
     */
    protected $casts = [
        'challonge_id' => 'integer',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'nickname',
        'user_uuid',
        'tournament_uuid',
        'challonge_id',
    ];
}
