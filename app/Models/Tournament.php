<?php
declare(strict_types = 1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Class Tournament
 * @package App\Models
 *
 * @property string title
 * @property string description
 * @property string sport
 * @property string category
 * @property integer price
 * @property string currency
 * @property string creator_uuid
 * @property string tournament_type_uuid
 * @property boolean third_place_match
 * @property integer players_count_in_group
 * @property integer players_count_in_playoff
 * @property string metadata
 * @property string state
 * @property Carbon|string started_at
 *
 * @property TournamentPlayer[] players
 *
 * @OA\Schema(
 *      allOf={
 *          @OA\Schema(
 *              @OA\Property(
 *                  property="title",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="description",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="sport",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="category",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="price",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="currency",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="creator_uuid",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="tournament_type_uuid",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="third_place_match",
 *                  type="boolean",
 *              ),
 *              @OA\Property(
 *                  property="players_count_in_group",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="players_count_in_playoff",
 *                  type="integer",
 *              ),
 *              @OA\Property(
 *                  property="metadata",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="state",
 *                  type="string",
 *              ),
 *              @OA\Property(
 *                  property="started_at",
 *                  type="string",
 *              ),
 *          ),
 *          @OA\Schema(ref="#/components/schemas/BaseModel"),
 *      }
 * )
 */
class Tournament extends BaseModel
{
    /**
     * @var string
     */
    protected $table = 'tournaments';

    /**
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime:Y-m-d H:i:s',
        'price' => 'integer',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'sport',
        'category',
        'price',
        'currency',
        'creator_uuid',
        'tournament_type_uuid',
        'third_place_match',
        'players_count_in_group',
        'players_count_in_playoff',
        'metadata',
        'state',
        'started_at',
    ];

    /**
     * @return HasMany
     */
    public function players(): HasMany
    {
        return $this->hasMany(TournamentPlayer::class);
    }
}
