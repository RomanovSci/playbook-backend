<?php

namespace App\Models;

/**
 * Class TournamentRequest
 * @package App\Models
 *
 * @property string tournament_uuid
 * @property string user_uuid
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
 *                  property="user_uuid",
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
class TournamentRequest extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments_requests';
}
