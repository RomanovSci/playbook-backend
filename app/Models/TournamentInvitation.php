<?php

namespace App\Models;

/**
 * Class TournamentInvitation
 * @package App\Models
 *
 * @property string tournament_uuid
 * @property string inviter_uuid
 * @property string invited_uuid
 * @property string approved_at
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="tournament_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="inviter_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="invited_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="approved_at",
 *                  type="string",
 *              )
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel")
 *      }
 * )
 */
class TournamentInvitation extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_invitations';
}
