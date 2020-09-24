<?php
declare(strict_types = 1);

namespace App\Models;

/**
 * Class TournamentType
 * @package App\Models
 *
 * @property string title
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="title",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class TournamentType extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournament_types';
}
